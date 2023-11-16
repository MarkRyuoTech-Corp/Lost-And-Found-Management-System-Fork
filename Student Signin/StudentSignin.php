<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>
<body>
    <h2>Login</h2>

    <?php
    // PHP code for handling form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $srCode = $_POST["sr_code"];
        $password = $_POST["password"];

        // Validate the login
        if (loginUser($srCode, $password)) {
            echo "<p>Login successful!</p>";
        } else {
            echo "<p>Login failed. Please check your Sr_code and password.</p>";
        }
    }

    function loginUser($srCode, $password) {
        // Implement your database connection
        $servername = "localhost";
        $dbname = "db_nt3102";
        $username_db = "Sr_code";
        $password_db = "password";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if the Sr_code exists in the database
            $stmt = $conn->prepare("SELECT * FROM Student WHERE Sr_code = :srCode");
            $stmt->bindParam(':srCode', $srCode);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Sr_code exists, check the password
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['password'] === $password) {
                    return true; // Login successful
                }
            } else {
                // Sr_code doesn't exist, create a new account
                $stmt = $conn->prepare("INSERT INTO Student (Sr_code, password) VALUES (:srCode, :password)");
                $stmt->bindParam(':srCode', $srCode);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                return true; // Login successful after account creation
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        } finally {
            $conn = null;
        }

        return false;
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="sr_code">Sr_code:</label>
        <input type="text" name="sr_code" required>

        <br>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
