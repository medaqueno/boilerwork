<- [back to index](../Readme.md)

## Projections/Views

**Ports: HTTP**

#### List All Tenants
`GET` **/tenants**

**Permissions**

- CanManageAll

```json
{
"data": 
    [
        {
            "id": "f1367c47-4371-4dab-a662-fa84db03b8fd",
            "legalIdentification": {
               "type": "NIF",
                "value": "B01830033",
            },
            "name": "Gutmann Group",
            "region": "EU",
            "country": "ES",
            "status": "ACTIVE",
            "created_at": "2021-01-03T02:30:00+01:00",
            "updated_at": "2021-01-03T02:30:00+01:00",
        },
        {
            "id": "6ad95249-b43e-477e-a41d-0fade07f59ad",
            ...
        },
    ]
}
```

#### Retrieve Tenant Detail By Tenant Id
`GET` **/tenant/{tenantId}**

**Permissions**

- CanManageAll,CanEditTenant

```json
{
"data": 
    {
        "id": "f1367c47-4371-4dab-a662-fa84db03b8fd",
        "legalIdentification": {
            "type": "NIF",
            "value": "B01830033",
        },
        "name": "Gutmann Group",
        "region": "EU",
        "country": "ES",
        "status": "ACTIVE",
        "created_at": "2021-01-03T02:30:00+01:00",
        "updated_at": "2021-01-03T02:30:00+01:00",
    }
}
```

#### List All Branches
`GET` **/braches**

**Permissions**

- CanManageBranches

```json
{
    "data": [
        {
            "id": "b949a776-3a0d-4270-bef0-8ddac5c125e3",
            "tenantId": "8227b53e-ef03-4687-9ca2-ddbe405511aa",
            "name": "Mitchell Group",
            "private": true,
            "status": "active",
            "created_at": "2022-07-19T17:11:09+00:00",
            "updated_at": "2022-07-19T17:11:09+00:00"
        },
        {
            "id": "2d9178e5-d01b-4fcf-8c0c-69cc0e993023",
            "tenantId": "8227b53e-ef03-4687-9ca2-ddbe405511aa",
            "name": "Bradtke - Luettgen",
            "private": true,
            "status": "active",
            "created_at": "2022-07-19T17:17:22+00:00",
            "updated_at": "2022-07-19T17:17:22+00:00"
        }
    ]
}
```

#### List All Users
`GET` **/users**

**Permissions**

- CanManageUsers

```json
{
    "data": [
        {
            "id": "a4398313-334b-4325-9b5c-0450a5669fbd",
            "branchId": "6a209af9-22a3-4592-b661-fba69b161f3d",
            "tenantId": "c06b3204-03bb-4aae-bcb6-11ddd9756e3c",
            "name": {
                "firstName": "Holly",
                "middleName": "",
                "lastName": "Little",
                "lastName2": ""
            },
            "email": "Keeley.Herman@hotmail.com",
            "status": "active",
            "created_at": "2022-07-19T16:57:03+00:00",
            "updated_at": "2022-07-19T16:57:03+00:00"
        },
        {
            "id": "f86feb6d-ce0b-45e5-b455-76c8d0b50c24",
            "branchId": "e7959989-5b9e-4055-9a75-a68d26c0eee6",
            "tenantId": "8227b53e-ef03-4687-9ca2-ddbe405511aa",
            "name": {
                "firstName": "Ernestina",
                "middleName": "",
                "lastName": "Murphy",
                "lastName2": ""
            },
            "email": "Johan78@gmail.com",
            "status": "active",
            "created_at": "2022-07-19T17:01:25+00:00",
            "updated_at": "2022-07-19T17:01:25+00:00"
        }
    ]
}
```
