csc309 a2 by Charuvit Wannissorn (1000149341)

I implemented the website in the most straight-forward way possible, using mostly PHP and Javascript and following the structure of the provided code. 
Everything should be self-explanatory. 
-To access the admin page, go to appname/admin.php.
-Server-side Form Validation done using an array. See config/form_validation.php.
-Client-side Form Validation done using Javascript.
-Printing functionality done using one line of Javascript.
-Seat selection done using HTML canvas and some javascript event handlers (onClick).

The MVC Structure:

Models:
-movie
-showtime
-theater
-ticket

Controllers:
-main
--index -> creates UI
--admin -> creates UI
--showShowtimes -> creates UI
--showTickets -> creates UI
--populate
--delete
--selectShowtime -> creates UI
--seleactSeat -> -> creates UI
--payment -> creates UI
--validate
--exp_check

Views (templates):
-index
-admin
-payment: information validation
-selectShowtime
-selectSeat
-showtimes: show all showtimes in the database
-tickets: show all tickets in the database
-summary: show receipt and ticket details


