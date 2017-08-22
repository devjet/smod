SMOD PRoject 0.1a
=============

Result of 2 day successfully done (Advanced) work on competition task.

#### Storing and retrieving message data 
Objectives:
------
###### Required (Middle): 
>Using existing DataBase model, create api backend for retrieving messages.
>Each message has member, group of memebers, message group, message service and can have an attachment
>Message is a part of conversation. Each member have device.
>API Communication should be protected and authorized by the tokens.

###### Advanced (Senior):
>all above, plus optimized and normalized DataBase structure.


Example of 2 day work on refactoring message saving and retrieving methods.
One day was spent on creating appropriate and normalized DB structure, another one for php-based backend.


Installation and usage
------------
1. Setup on apache "smod" host, and put content of this directory inside of www document_root
2. Setup connection to postgres on smod/config/setting.php and fill using db_dump_smod.sql
3. Open http://smod and get "It works!"

4. To test tokens    
      4. Open Postman and get one token: POST ```http://smod/api/v1/authenticate```
      4. Authorization: ```Basic, Username:Password test:test```
      4. Headers: 
	```Content-Type application/json```	

Hit Send, example responce:
```json
{
  "status": "ok",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0ODQ2NjkxNDIsImV4cCI6MTQ4NDY3NjM0MiwianRpIjoiM05FVmNZdTRDRzdxQzNzdmV6c29zViIsInN1YiI6InRlc3QiLCJzY29wZSI6WyJzY29wZS5SX09ORSIsInNjb3BlLlJXX09ORSIsInNjb3BlLlJXX0FMTCJdfQ.FuDjtK7v3_4G8DqgUDk3suAZQ58IehoruAulJCP_moI"

}
```

Then get token data and setup authorized request:
	```GET http://smod/api/v1/conversations```
```	
	Headers:
	 Content-Type application/json
	 Authorization  Bearer eyJ0eXAiOiJKV1QiLCJhbGcidOiJIUzI1NiJ9.eyJpYXQiOjE0ODQ2NjU0NjEsImV4cCI6MTQ4NDY3MjY2MSwianRpIjoiNmI0TGRzVkpEZURwTkNTZVVya2JhNyIsInN1YiI6InRlc3QiLCJzY29wZSI6WyJzY29wZS5SX09ORSIsInNjb3BlLlJXX09ORSIsInNjb3BlLlJXX0FMTCJdfQ.JkO8ZNjGTOts1sw-baWOA29J4qA-74L8sW3P6tMltHw
```

Hit Send, then you'll get message.
Task was done on "advanced" level.
