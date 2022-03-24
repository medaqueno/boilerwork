-- example HTTP POST script which demonstrates setting the
-- HTTP method, body, and adding a header

wrk.method = "POST"
wrk.body   = "{\"email\":\"email@test.pangea.es\",\"username\":\"nombreUsuario\",\"id\":\"3b18ed39-faee-4357-8b53-b36c1c195c05\"}"
wrk.headers["Content-Type"] = "application/json"
