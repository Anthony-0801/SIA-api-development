# API Documentation

## 1. Add Student Record
The postAdding endpoint allows you to add a new student record to the system.

### Endpoint:
URL: http://127.0.0.1/SIA-api-development/public/postAdding
### Method: POST
### Request Payload:
```{
  "status": "success",
  "data": {
    "studentname": "Student Name",
    "studentId": "201-1509-2",
    "section": "B",
    "sem": "2nd",
    "year": "4th year",
    "amount": 25.00,
    "date": "2023-11-20",
    "office_in_charge": "jsites",
    "description": "not paid"
  }
}```

The provided JSON payload contains information about the student, including their name, ID, section, semester, year, payment amount, payment date, office in charge, and a description of payment status (“paid” or "not paid").

### Response Format:
The response will contain information about the success or failure of the operation.

### Usage Guidelines:
Ensure that the required fields are present in the request payload.
Use appropriate error handling for failed requests.

## 2. Retrieve Student Records
The postretrieve endpoint allows you to retrieve a list of student records based on the specified page and limit parameters.

### Endpoint:
URL: http://127.0.0.1/SIA-api-development/public/postretrieve?page=1&limit=25
### Method: GET
### Request Payload:
No request payload for GET requests.
### Response Format:
The response will contain a list of student records based on the specified page and limit.
### Usage Guidelines:
Adjust the query parameters to paginate through the records.

## 3. View Student Record
The postView endpoint provides detailed information about a specific student record.
### Endpoint:
URL: http://127.0.0.1/SIA-api-development/public/postView
### Method: GET
### Request Payload:
No request payload for GET requests.
###Response Format:
The response will contain detailed information about a specific student record.
### Usage Guidelines:
Use this endpoint to view detailed information about a particular student.

##4. Update Student Record
The postupdate endpoint allows you to update an existing student record with new information.
### Endpoint:
URL: http://127.0.0.1/SIA-api-development/public/postupdate
### Method: POST
### Request Payload:
{
  "status": "success",
  "data": {
    "studentname": "Jaynard A. Raqueño",
    "studentId": "201-1509-2",
    "section": "B",
    "sem": "2nd",
    "year": "4th year",
    "amount": 25.00,
    "date": "2023-11-20",
    "office_in_charge": "jsites",
    "description": "paid"
  }
}
### Response Format:
The response will contain information about the success or failure of the update operation.
Usage Guidelines:
Ensure that the student record with the specified ID exists before attempting an update.

## 5. Delete Student Record
The postdelete endpoint allows you to delete a student record based on the provided student ID.
### Endpoint:
URL: http://127.0.0.1/SIA-api-development/public/postdelete
### Method: DELETE
### Request Payload:

```{
  "status": "success",
  "data": {
    "studentId": "201-1509-2"
  }
}```

### Response Format:
The response will contain information about the success or failure of the delete operation.
### Usage Guidelines:
Provide the student ID in the request payload to delete the corresponding student record.
Certainly! Here's the documentation for the printSummary endpoint:

## 6. Print Summary
The printSummary endpoint is designed to provide a summary of student records.
### Endpoint:
URL: http://127.0.0.1/SIA-api-development/public/printSummary
### Method: GET
### Request Payload:
No request payload for GET requests.
### Response Format:
The response will contain a summary of student records, such as the total number of records, total amount paid, and any other relevant summary information.