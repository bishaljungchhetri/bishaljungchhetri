<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
</head>
<body>
<h1>Users</h1>

<table>
  <thead>
    <tr>
       <th>Name</th>
       <th>Email</th>
       <th>Addres</th>
       <th>Phone</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($users as $user): ?>
    <tr>
    <td><?php echo $user->name; ?></td>
    <td><?php echo $user->email; ?></td>
    <td><?php echo $user->address; ?></td>
    <td><?php echo $user->phone; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
 <?php view('user/partial.php'); ?>






    
</body>
</html>