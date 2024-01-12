<?php
include_once 'partials/header.tpl.php';
?>
<body>
    <div class="container-fluid">
        <h1>User Book History</h1>
        <?php
        
          if(sizeof($userHistoryBooks) > 0){
            echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Id</th>
                <th scope="col">Book</th>
              </tr>
            </thead>
            <tbody>';
            $i = 1;
            foreach($userHistoryBooks as $book){
              echo "<tr>
              <td>$i</td>
              <td>$book->title</td>
              </tr>";
              $i++;
            }
              
          }
        ?>
        </tbody>
        </table>
    </div>
</body>

</html>
