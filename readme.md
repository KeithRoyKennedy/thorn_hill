A client wishes to regularly and automatically submit information about their staff to our
servers. 

You need to write a simple web service that will receive POST submissions of data,
and update appropriate database tables.
The data will be sent as a tab-separated text string, with entries for successive people
separated by newline characters (as though pasted from a spreadsheet). 

There will be a number of fields per person, namely:
1. First name
2. Surname
3. Email address
4. Gender (either the character “m” or the character “f”)
5. Department name
6. Department contact person name
7. Department contact person email address

Note that the contact person is always the same person (with the same email address) for a
given department.

You need to provide:
● SQL CREATE TABLE commands for appropriate table(s) in which to store the data
optimally.
● A brief description of the syntax (i.e., http query format) which clients of the
webservice system should use to provide the data to your script. This paragraph
should be sufficient for well-qualified developers at the customer to be able to write
the necessary code to use your web-service system.
● Well-structured PHP file(s) to receive queries and submit the data to the database,
free from security holes and using appropriate database access commands/libraries.
Your solution should include:
● Functionality to archive existing data when you receive a new set of people (you may
decide how you store previous data in the database that is no longer the most recent
batch).
● Functionality to insert the new data.
● Basic acknowledgement to the client that the data has been submitted.
Data validation is NOT required (beyond basic security) – you can assume that the input is
of the correct form.