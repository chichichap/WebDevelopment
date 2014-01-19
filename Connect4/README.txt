csc309 a3 by Charuvit Wannissorn (1000149341)

1. What I used to develop the website:
- CodeIgniter: platform
- PHP: for the infrastructure
- Javascript: for the game board visualization and functionality 
- JQuery + AJAX: for updating information without reloading the game page.
- JSON: for packing and unpacking PHP objects (boardstate) to save to the database.

2. What have been modified/added to the MVC Structure:
Models:
-boardstate (Class)
--dropDisc 
--fourDiscsConnected (return true/false)
-match_model
--updateBoardState

Controllers:
-board
--quit (accessible when the game finishes; to update user status)
--postTurn
--getTurn

Views (templates):
-board (added the gameboard functionality to the page)

3. Captcha For Account Creation
I did everything according to the instructions on the website, 
except that I changed the location of the downloaded file to be inside the project folder (connect4).
Folder location: connect4/securimage/

4. Other Notes
- Your discs are in yellow. Your opponent's are in red.
- Invitee is player 1 (gets to play first). The user numbers for status that come with the starter code are supposed to be reversed.
- The turn variable is equal the player number if it's the player's turn.
- The SMTP server has been set to gmail, and my gmail username and password are used in recoverPassword.
- Match status is updated as soon as a game is won. User status is updated after the user quits.
- json_encode in postTurn: the board state object is packed to a JSON string and stored in the database as a BLOB.
- json_decode in getTurn: the boardstate object is re-constructed using the JSON string from the database.



