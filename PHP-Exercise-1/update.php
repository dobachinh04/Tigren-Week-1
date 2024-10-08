<?php
session_start();

$user_id = $username = $email = $address = $phone = $gender = "";
$usernameErr = $emailErr = $addressErr = $phoneErr = $genderErr = "";

// Lấy thông tin người dùng từ session
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user = array_filter($_SESSION['users'], function ($user) use ($id) {
        return $user['user_id'] == $id;
    });
    $user = array_shift($user); // Lấy user đầu tiên
    if ($user) {
        $user_id = $user['user_id'];
        $username = $user['username'];
        $email = $user['email'];
        $address = $user['address'];
        $phone = $user['phone'];
        $gender = $user['gender'];
    } else {
        header('Location: index.php');
        exit;
    }
}

// Xử lý form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate và xử lý dữ liệu nhập
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $address = htmlspecialchars($_POST["address"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $gender = htmlspecialchars($_POST["gender"]);

    // Validate
    if (empty($username))
        $usernameErr = "Username cannot be empty.";
    if (empty($email))
        $emailErr = "Email cannot be empty.";
    if (empty($address))
        $addressErr = "Address cannot be empty.";
    if (empty($phone))
        $phoneErr = "Phone cannot be empty.";
    if (!is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 11)
        $phoneErr = "Phone must be a number with 10 to 11 digits.";
    if (empty($gender))
        $genderErr = "Please select a gender.";

    // Kiểm tra email trùng lặp (ngoại trừ chính nó)
    $emailExists = array_filter($_SESSION['users'], function ($user) use ($email, $user_id) {
        return $user['email'] == $email && $user['user_id'] != $user_id;
    });
    if (!empty($emailExists))
        $emailErr = "Email already exists.";

    // Nếu không có lỗi, cập nhật dữ liệu và chuyển hướng
    if (empty($usernameErr) && empty($emailErr) && empty($addressErr) && empty($phoneErr) && empty($genderErr)) {
        $_SESSION['users'] = array_map(function ($user) use ($username, $email, $address, $phone, $gender, $user_id) {
            if ($user['user_id'] == $user_id) {
                $user['username'] = $username;
                $user['email'] = $email;
                $user['address'] = $address;
                $user['phone'] = $phone;
                $user['gender'] = $gender;
            }
            return $user;
        }, $_SESSION['users']);
        header('Location: index.php');
        exit;
    }
}
?>

<?php include('layouts/header.php'); ?>

<h1 class="text-center">Update User: <?php echo $user_id; ?> - <?php echo $username; ?></h1>
<form method="POST" action="">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control <?php echo !empty($usernameErr) ? 'is-invalid' : ''; ?>" id="username"
            name="username" value="<?php echo $username; ?>">
        <div class="invalid-feedback"><?php echo $usernameErr; ?></div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control <?php echo !empty($emailErr) ? 'is-invalid' : ''; ?>" id="email"
            name="email" value="<?php echo $email; ?>">
        <div class="invalid-feedback"><?php echo $emailErr; ?></div>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control <?php echo !empty($addressErr) ? 'is-invalid' : ''; ?>" id="address"
            name="address" value="<?php echo $address; ?>">
        <div class="invalid-feedback"><?php echo $addressErr; ?></div>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control <?php echo !empty($phoneErr) ? 'is-invalid' : ''; ?>" id="phone"
            name="phone" value="<?php echo $phone; ?>">
        <div class="invalid-feedback"><?php echo $phoneErr; ?></div>
    </div>

    <div class="mb-3">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select <?php echo !empty($genderErr) ? 'is-invalid' : ''; ?>" id="gender" name="gender">
            <option disabled>Select Gender</option>
            <option value="Male" <?php echo $gender == "Male" ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo $gender == "Female" ? 'selected' : ''; ?>>Female</option>
        </select>
        <div class="invalid-feedback"><?php echo $genderErr; ?></div>
    </div>

    <a href="index.php" class="btn btn-secondary">Back</a>
    <button type="submit" class="btn btn-warning">Update</button>
</form>

<?php include('layouts/footer.php'); ?>