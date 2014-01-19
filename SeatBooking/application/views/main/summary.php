<?php 
echo anchor('','Home') . "<br><br>";

echo 'Receipt:';
echo '<br> Ticket Price: $6 <br>';

echo '<br> First name: ' . $first;
echo '<br> Last name: ' . $last;

echo '<br><br> Movie: ' . $title;
echo '<br> Theater: ' . $name;
echo '<br> Address: ' . $address;
echo '<br> Date: ' . $date;
echo '<br> Time: ' . $time;

echo '<br><br> Seat Number: ' . $seat;
?>

<br/><br><A HREF="javascript:window.print()">Print This Page</A>