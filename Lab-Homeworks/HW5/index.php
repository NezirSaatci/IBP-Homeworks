<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php

    // veri tabanına bağlanma
    $db = new mysqli('hostname', 'username', 'password', 'database_name');

    // tablo oluşturma
    $db->query("CREATE TABLE students
    (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255),
        email VARCHAR(255),
        gender ENUM('Male', 'Female');
    )");

    // formun gönderilip gönderilmediğini kontrol 
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $errors = [];
        if (empty($_POST['full_name']))
        {
            $errors[] = "Full name is required";
        }

        if (empty($_POST['email']))
        {
            $errors[] = "Email is required";
        }

        elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            $errors[] = "Invalid email format";
        }

        if (empty($_POST['gender']))
        {
            $errors[] = "Gender is required";
        }

        elseif (!in_array($_POST['gender'], ['Male', 'Female']))
        {
            $errors[] = "Invalid gender value";
        }

        // hata olup olmadığı kontrol edildikten sonra verileri veri tabanına girme
        if (empty($errors))
        {
            $stmt = $db->prepare("INSERT INTO students (full_name, email, gender) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['full_name'], $_POST['email'], $_POST['gender']);

            if ($stmt->execute())
            {
                echo "<p>Student information successfully inserted into the database.</p>";
            }
            else
            {
                echo "<p>An error occurred while inserting the data into the database.</p>";
            }
            $stmt->close();
        }

        else
        {
            // hata mesajlarını görüntüleme
            echo "<ul>";
            foreach ($errors as $error)
            {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }
    }

    // veri tabanından bilgileri alıp sayfada görüntüleme
    $result = $db->query("SELECT * FROM students");
    if ($result->num_rows > 0)
    {
        echo "<table>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Email</th><th>Gender</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['full_name']}</td><td>{$row['email']}</td><td>{$row['gender']}</td></tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>No students found in the database.</p>";
    }

    ?>

    <!-- HTML ile form kısmı -->
    <form method="post">

        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br>

        <label>Cinsiyet:</label><br>

        <input type="radio" id="erkek" name="cinsiyet" value="Erkek">
        <label for="erkek">Erkek</label><br>

        <input type="radio" id="kadin" name="cinsiyet" value="Kadin">
        <label for="kadin">Kadin</label><br>

        <input type="submit" value="Submit">

    </form>

</body>
</html>