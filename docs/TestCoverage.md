<- [back to index](../Readme.md)

## Test Coverage

These tests check that entities are in their correct state, they are not persisted.

### Create Tenant
- Entity is created correctly from scratch.
- Entity is version 1.
- Event TenantWasAdded exists.
- Event TenantWasAdded property **permissions** contains correct values (in any order): CanEditTenant, CanManageBranches and CanManageUsers as default permissions; CanAccessRetailer as an extra permission added manually.
- Event TenantWasAdded property **status** is Active.

### Disable Tenant
- Entity is version 2.
- Event TenantWasDisabled exists.
- Event TenantWasDisabled property **status** is Disabled
- Event TenantWasDisabled property **tenantId** is the same as initial target aggregate Id

### Modify Tenant Data
- Entity is version 2.
- Entity total recorded events is 2.
- Event TenantDataWasModified exists.
- Event TenantDataWasModified properties **name**, **legalIdentificationType**, and **legalIdentificationValue**, **country** are equal to new values.
- Event TenantDataWasModified property **tenantId** is the same as initial target aggregate Id

### Disabled Tenant can not be modified
- Entity can not be modified. Check a CustomAssertionFailedException is thrown.
- Entity is still version 2.
- Entity total recorded events is 2.
- Entity has two events: Created and Disabled.

### Create Branch
- Entity is created correctly from scratch.
- Entity is version 1.
- Event BranchWasAddedToTenant exists.
- Event BranchWasAddedToTenant property **permissions** contains correct values (in any order): CanEditBranch and CanManageUsers as default permissions; CanAccessRetailer as an extra permission added manually.
- Event BranchWasAddedToTenant property **status** is Active.

### Disable Branch
- Entity is version 2.
- Event BranchWasDisabled exists.
- Event BranchWasDisabled property **status** is Disabled
- Event BranchWasDisabled property **branchId** is the same as initial target aggregate Id

### Modify Branch Data
- Entity is version 2.
- Entity total recorded events is 2.
- Event BranchDataWasModified exists.
- Event BranchDataWasModified properties  **name** is equal to new value.

### Modify Branch Visibility
- Entity is version 2.
- Entity total recorded events is 2.
- Event BranchVisibilityWasModified exists.
- Event BranchVisibilityWasModified properties **private** is equal to new value.

### Disabled Branch can not be modified
- Entity can not be modified. Check a CustomAssertionFailedException is thrown.
- Entity is still version 2.
- Entity total recorded events is 2.
- Entity has two events: Created and Disabled.

--- 

### Create User
- Entity is created correctly from scratch.
- Entity is version 1.
- Event UserWasAddedToBranch exists.
- Event UserWasAddedToBranch property **permissions** contains correct values (in any order): CanEditUser as default permissions; CanAccessRetailer as an extra permission added manually.
- Event UserWasAddedToBranch property **status** is Active.

### Disable User
- Entity is version 2.
- Event UserWasDisabled exists.
- Event UserWasDisabled property **status** is Disabled
- Event UserWasDisabled property **userId** is the same as initial target aggregate Id

### Modify User Data
- Entity is version 3.
- Entity total recorded events is 3.
- Event UserDataWasModified and UserEmailWasModified exist.
- Event UserDataWasModified properties **firstName**, **middleName**, **lastName**, **lastName2** are equal to new values.
- Event UserEmailWasModified properties **email** is equal to new value.

### Disabled User can not be modified
- Entity can not be modified. Check a CustomAssertionFailedException is thrown.
- Entity is still version 2.
- Entity total recorded events is 2.
- Entity has two events: Created and Disabled.
