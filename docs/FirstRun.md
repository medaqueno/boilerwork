<- [back to index](../Readme.md)

## First Run

### Clone Repository

Repository instructions can be found in: 

https://eu-west-1.console.aws.amazon.com/codesuite/codecommit/repositories/Quadrant-Domain-Users/connect?region=eu-west-1

```bash
$ git clone ssh://git-codecommit.eu-west-1.amazonaws.com/v1/repos/Quadrant-Domain-Users .
```

### Start containers

``` bash
$ cd src/docker 

$ docker compose up --build
```

### Database Migration and Seeding

Migration files are found in */src/migrations* folder. 
They should be prefixed chronologically, so they can be executed in order.

#### Master User Data

Master Tenant/Branch/User will be created with following data:

- TenantId: b6699fce-244d-41d3-a6f5-708417455548
- BranchId: 0c5ade88-6e6e-423d-87e7-cc26c9caec6d
- UserId: 52c7410f-9d21-4357-b81d-de6370bf06d1

- Tenant Name: Quadrant
- Branch Name: Main
- User Name: Admin Quadrant
- User Email: it.pangea@pangea.es

- Permissions: CanManageAll

Credentials must be created in Auth Service API.
