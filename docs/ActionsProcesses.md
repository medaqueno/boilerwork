<- [back to index](../Readme.md)

## Actions and Processes

All actions that can alter the state of the system.

![ActionsEvents Image](./img/Clients-ActionEvents.svg)

### Create Tenant
`Sync`
Create a Tenant entity.
CanEditTenant, CanManageBranches and CanManageUsers permissions are granted automatically.

**Ports:**
- HTTP

**Events Published:**
- TenantWasAdded

**Business Rules**
- Tenant cannot be created if another Tenant has the same LegalIdentification in the system.

**Permissions**
- CanManageAll

**Input**
```
<string> tenantId UUIDv4
array <string> permissions [CanAccessRetailer and/or CanAccessWholesaver]
<string> name
<string> legalIdentificationType [CIF,NIF]
<string> legalIdentificationValue
<string> region
<string> country ISO 3166 Alpha Code-2
```

### Modify Tenant Data
`Async`
Updates data related to Tenant Entity.
**Ports:**
- HTTP

**Events Published:**
- TenantDataWasModified

**Business Rules**
- Tenant cannot modify its LegalIdentification if another Tenant has the same LegalIdentification in the system
- Tenant cannot be modified if `status !== active`

**Permissions**
- CanManageAll,CanEditTenant

**Input**
```
<string> tenantId UUIDv4
<string> name
<string> legalIdentificationType [CIF,NIF]
<string> legalIdentificationValue
<string> country ISO 3166 Alpha Code-2
```

### Disable Tenant
`Async`
Change status property to *disabled*. 
A disabled Tenant and its children entities will not be able to operate with any service.

**Ports:**
- HTTP

**Events Published:**
- TenantWasDisabled

**Business Rules**
- None

**Permissions**
- CanManageAll,CanEditTenant

**Input**
```
<string> tenantId UUIDv4
```

<br /><br />

### Add Branch to Tenant
`Sync`
Create a Branch entity and relate it to parent Tenant.
CanEditTenant and CanManageUsers permissions are granted automatically.

**Ports:**
- HTTP

**Events Published:**
- BranchWasAddedToTenant

**Business Rules**
- None

**Permissions**
- CanManageBranches

**Input**
```
<string> branchId UUIDv4
<string> tenantId UUIDv4
array <string> permissions [Pending]
<string> name
<boolean> private
```

### Modify Branch Data
`Async`
Updates data related to Branch Entity.

Modifying *private* property results in: 
- Allowing/disallowing other Branches of the same Tenant to search/access its data.


**Ports:**
- HTTP
 
**Events Published:**
- BranchDataWasModified

**Business Rules**
- Branch cannot be modified if `status !== active`

**Permissions**
- CanManageBranches,CanEditBranch

**Input**
```
<string> branchId UUIDv4
<string> name
```

### Modify Branch Visibility
`Async`
Change the branch visibility (*private* property) .

Modifying visibility results in: 
- Allowing/disallowing other Branches of the same Tenant to search/access its data and resources.

**Ports:**
- HTTP
 
**Events Published:**
- BranchVisibilityWasModified  (If _private_ property was changed in the process)

**Business Rules**
- Branch cannot be modified if `status !== active`

**Permissions**
- CanManageBranches,CanEditBranch

**Input**
```
<string> branchId UUIDv4
<boolean> private
```

### Disable Branch
`Async`
Change status property to *disabled*. 
A disabled Branch and its children entities will not be able to operate with any service.

**Ports:**
- HTTP

**Events Published:**
- BranchWasDisabled

**Business Rules**
- None

**Permissions**
- CanManageBranches,CanEditBranch

**Input**
```
<string> branchId UUIDv4
```

### Add User to a Branch
`Sync`
Create a User entity and relate it to parent Branch.

**Ports:**
- HTTP

**Events Published:**
- UserWasAddedToBranch

**Business Rules**
- User cannot be added if another User has the same email in the system.

**Permissions**
- CanManageUsers

**Input**
```
<string> userId UUIDv4
<string> branchId UUIDv4
<string> tenantId UUIDv4
array <string> permissions
<string> firstName
<?string> middleName
<string> lastName
<?string> lastName2
<string> email
```

### Modify User Data
`Async`
Updates data related to User Entity.

**Ports:**
- HTTP

**Events Published:**
- UserDataWasModified
- UserEmailWasModified (If email property was changed in the process)

**Business Rules**
- User cannot modify its email if another User has the same email in the system
- User cannot be modified if `status !== active`

**Permissions**
- CanManageUsers,CanEditUser

**Input**
```
<string> userId UUIDv4
<string> firstName
<?string> middleName
<string> lastName
<?string> lastName2
<string> email
```

### Disable User 
`Async`
Change status property to *disabled*. 
A disabled User will not be able to operate with any service.

**Ports:**
- HTTP

**Events Published:**
- UserWasDisabled

**Business Rules**
- None

**Permissions**
- CanManageUsers,CanEditUser

**Input**
```
<string> userId UUIDv4
```

## Background Processes
None.

## Subscriptions to Events
- TenantWasAdded 
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllTenantsProjection
- TenantWasDisabled
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllTenantsProjection
- BranchWasAddedToTenant 
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllBranchesProjection
- BranchWasDisabled
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllBranchesProjection
- BranchVisibilityWasModified
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllBranchesProjection
- UserWasAddedToBranch 
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllUsersProjection
- UserWasDisabled
    - \App\Core\ExampleBoundedContext\Infra\Projections\AllUsersProjection
