## Simple PHP API CRUD

Consume this endpoint using tools similar like Postman. I'm using XAMPP and PHPMyAdmin for this project.
This is an example for a json to send or the response in the GET methods.

`
{
      "id": "1",
      "name": "Felix Veras",
      "phone": "8098098009",
      "email": "felixveras@yopmail.com",
      "address": "Calle luna calle sol"
  }
`

**HTTP Methods**

*GET*

> Return all contacts "HTTP GET"
`http://localhost/contact_list_api/api.php/contacts`

> Return single contact by Id "HTTP GET"
`http://localhost/contact_list_api/api.php/contacts/1`

*POST*

> Add new contact "HTTP POST"
`http://localhost/contact_list_api/api.php/contacts`

*PUT*

> Update existing contact "HTTP PUT"
`http://localhost/contact_list_api/api.php/contact/1`

*DELETE*

> Delete a contact "HTTP DELETE"
`http://localhost/contact_list_api/api.php/contact/1`
