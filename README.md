# Code review task

## Task definition

 * Create REST api create customer notifications by email or sms depends on customer settings.
 * Customer profile data is saved in database

 Request:
     url: /api/customer/{code}/notifications
    json: { body: "notification text"  }