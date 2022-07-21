## Git Best Practices (In development)

### Enter personal info
```bash
git config --global user.name "David Hasselhoff"
git config --global user.email "david.hasselhoff@knightrider.com"
```

### Branches
#### Default (Long running) Branches

1. **master** -> Production Environment. Holds always valid and tested code.
2. **stage** -> QA/Staging Environment. Hold latest developments ready to be released. Here is where QA / Business people test. Releases should be generated from this branch.
3. **dev** -> Development Environment. Tasks that are currently being developed must be deployed here to be accessible to allow other dev teams to check their own local branches against. It's an optional branch that is prone to hold some mixed, unfinished or even buggy functionality.

> Other branches could exist, it depends on each project. It could be even recommendable that only two branches exist.

#### Best practices working with branches

- **NEVER** merge any long running branch between themselves. Example: Do not merge **dev** into **stage**.
- **ONLY** **stage** can (and must) be merged with **master** once is tested and approved by QA/Business staff.
- **ALWAYS** update **master** and **stage** before any operation involving create new branches or merge/rebase.
- **ALWAYS** rebase temp development branches before merge into long running branches.
- **ONLY** work on development branches (features, fixes, etc.).

#### Name branches like a boss (Temp development branches)
- Always create a new branch from master for each new feature or fix.
- Name and prefix the branches properly. Names should be descriptive and/or reference to the assigned task or epic. For example, use the task title as branch name.
    - feature/`<taskNumber>`-`<improve-performance-fetching-x-api>`
    - hotfix/`<taskNumber>`-`<change-base-url>`

### Commits

#### Commit the right way
- Many small commits. It helps to track changes and makes easier to review the work.
- Commit messages should include the reason of the changes, why we did something. Messages that describe what we have done in code or files are useless, we can already see changes with `git diff --staged`
    Good message:
    ```
    Chore: Improve performance fetching data from X API adding async threads.
    
    Link: https://redmine.pangea.es/issues/2291
    Task Addressed: Feature #2291
    ```
    > Include a link to the task if it is possible.
    
    Bad messages:
    ```
    Remove ThisFile.json
    ```
    ```
    Fix Bugs
    ```
> As a rule of thumb, if you execute **git log --oneline** or **git shortlog**, the history should be readable and easy to understand.

> Good to read: https://cbea.ms/git-commit/

#### Create new feature
1. Update master and create a new branch from it. 
    ```bash
    $ git fetch origin master:master
    $ git checkout -b feature/new-feature-1 master
    ```

2. When work is finished:
    ```bash
   # Update long running branches from any branch
    $ git fetch origin master:master && git fetch origin stage:stage
    $ git checkout feature/new-feature-1 master
    # Rebase your temp development branch. Conflicts may appear.
    $ git rebase master
    $ git checkout stage
    # Merge your branch into pre squashing all commits into one, making easier to revert the feature if needed.
    $ git merge --squash feature/new-feature-1
    # Add a good message as indicated before in this page (Commit the right way)
    $ git commit 
    # Update remote branch
    $ git push origin stage
    $ git branch -d feature/new-feature-1
    ```
    > If **git push** is rejected because it's not a fast-forward merge, then **git pull --rebase origin stage** and repeat push.

#### Deploy to production. Create a release and tag.
1. After finishing multiple features, we can make a release before publishing and deploying to Production.

    ```bash
    # Update long running branches from any branch
    $ git fetch origin master:master && git fetch origin stage:stage
    # Tag
    $ git tag 2.3.0 (Major.Minor.Fixes)
    $ git push --tags origin stage
    # Merge to master Fast Forward Only
    $ git checkout master
    $ git merge --ff-only 2.3.0
    $ git push origin master
    ```

> Semantic versioning: https://semver.org/

#### Starting a hotfix
1. Update master and create a new branch from it. Always from **master**.
    ```bash
    # Update long running branches from any branch
    $ git fetch origin master:master
    $ git checkout -b hotfix/api-x-connection-fails
    ```

2. When work is finished:
    ```bash
    # Update long running branches
    $ git fetch origin master:master && git fetch origin stage:stage
    $ git checkout -b hotfix/api-x-connection-fails master
    # Tag
    $ git tag 2.3.1 (Major.Minor.Fixes)
    $ git checkout stage
    # Merge your branch.
    $ git merge hotfix/api-x-connection-fails
    $ git push --tags origin stage
    $ git branch -d hotfix/api-x-connection-fails
    # Merge to master Fast Forward Only
    $ git checkout master
    $ git merge --ff-only 2.3.1
    $ git push origin master
    ```

### Tricks:

#### Different (and maybe easier) visualizations of git log
Based on: https://stackoverflow.com/questions/1057564/pretty-git-branch-graphs. 
``` bash
# Add to ~/.gitconfig:
[alias]
    log1 = log --graph --abbrev-commit --decorate --all
    log2 = log --graph --abbrev-commit --decorate --all --simplify-by-decoration # Show only last commit of each branch
# Or execute:
git config --global alias.log1 "log --graph --abbrev-commit --decorate --all"
git config --global alias.log2 "log --graph --abbrev-commit --decorate --all --simplify-by-decoration"
```
> Where log1 and log2 are the names of the alias.

#### Remove pager listing branches.
``` bash
# Add to ~/.gitconfig:
[pager]
    branch = false
# Or execute:
git config --global pager.branch false
```

#### Make rebase by default on pull
``` bash
git config --global pull.rebase true
```

### Links:

- Cheat Sheet: 
    - https://www.freecodecamp.org/news/git-cheat-sheet/
    - https://dev.to/doabledanny/git-cheat-sheet-50-commands-free-pdf-and-poster-4gcn
    - In PDF: https://www.atlassian.com/git/tutorials/atlassian-git-cheatsheet
- Good commit messages: https://cbea.ms/git-commit/
- Examples of Git flows: 
    - https://nvie.com/posts/a-successful-git-branching-model/
    - https://www.endoflineblog.com/oneflow-a-git-branching-model-and-workflow
- Oh shit, I think I messed up!: https://ohshitgit.com/
