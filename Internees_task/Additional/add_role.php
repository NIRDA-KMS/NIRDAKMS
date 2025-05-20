<?php
include '../NIRDAKMS/SchedureEvent/connect.php';
include "header.php";

// Debug: Check if database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get all permissions
function getPermissions($conn) {
    $permissions = array();
    $sql = "SELECT permission_id, permission_name, description FROM permissions ORDER BY permission_name ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
    }
    return $permissions;
}

$permissions = getPermissions($conn);

// Debug: Print permissions
echo "<!-- Debug: Permissions array: ";
print_r($permissions);
echo " -->";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_name = $_POST['role_name'];
    $description = $_POST['description'];
    $selected_permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Validate role name
    if (strlen($role_name) < 3 || strlen($role_name) > 50) {
        die('Role name must be between 3 and 50 characters.');
    }

    // Check if role name is unique
    $stmt = $conn->prepare("SELECT role_id FROM roles WHERE role_name = ?");
    $stmt->bind_param("s", $role_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die('Role name already exists.');
    }
    $stmt->close();

    // Validate description
    if (strlen($description) > 500) {
        die('Description must not exceed 500 characters.');
    }

    // Validate permissions
    if (empty($selected_permissions)) {
        die('At least one permission must be selected.');
    }

    // Insert new role
    $stmt = $conn->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $role_name, $description);
    
    if ($stmt->execute()) {
        $role_id = $conn->insert_id;
        
        // Insert role permissions
        $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($selected_permissions as $permission_id) {
            $stmt->bind_param("ii", $role_id, $permission_id);
            $stmt->execute();
        }
        
        echo "Role added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Role</title>
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --background-color: #ecf0f1;
            --text-color: #34495e;
        }
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            max-width: 620px;
            margin: auto;
            padding: 10px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
		h4 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        .form-group {
            margin-bottom: 10px;
            position: relative;
        }
        .input-wrapper {
			position: relative;
			padding-top: 60px; /* Add some padding at the top */
		}

		.input-wrapper input,
		.input-wrapper textarea {
			width: 100%;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			box-sizing: border-box;
			font-size: 16px;
			transition: 0.2s ease all;
		}

		.input-wrapper label {
			position: absolute;
			left: 10px;
			top: 25px; /* Adjust this value */
			color: #999;
			font-size: 16px;
			transition: all 0.3s;
			pointer-events: none;
		}

		.input-wrapper input:focus ~ label,
		.input-wrapper textarea:focus ~ label,
		.input-wrapper input:not(:placeholder-shown) ~ label,
		.input-wrapper textarea:not(:placeholder-shown) ~ label {
			top: 0;
			left: 5px;
			font-size: 12px;
			color: var(--primary-color);
			background-color: white;
			padding: 0 5px;
		}
				
        .textarea {
            min-height: 100px;
            resize: vertical;
        }
        .permissions-container {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .permission-item {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 0px;
            margin-bottom: 5px;
            transition: box-shadow 0.3s ease;
        }
        .permission-item:hover {
            box-shadow: 0 2px 5px var(--primary-color);
        }
        .permission-item input[type="checkbox"] {
            margin-right: 5px;
        }
        .permission-name {
            font-weight: 600;
            color: var(--primary-color);
        }
        .permission-description {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 5px;
            margin-left: 0px;
        }
        button {
            display: inline-block;
            background: #1a237e;
            color: #ffffff;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Role</h2>
        <form id="addRoleForm" method="post" action="add_role.php">
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" id="role_name" name="role_name" required minlength="3" maxlength="50" placeholder=" ">
                    <label for="role_name">Role Name</label>
                </div>
            </div>
            <div class="form-group">
                <div class="input-wrapper">
                    <textarea id="description" name="description" placeholder=" " maxlength="500"></textarea>
                    <label for="description">Description</label>
                </div>
            </div>
            <div class="form-group">
                <label style="position: static; transform: none; margin-bottom: 5px; display: block;"><h4>Permissions</h4></label>
                <div class="permissions-container">
                    <?php if (!empty($permissions)): ?>
                        <?php foreach($permissions as $permission): ?>
                            <div class="permission-item">
								<input type="checkbox" id="permission_<?php echo $permission['permission_id']; ?>" name="permissions[]" value="<?php echo $permission['permission_id']; ?>">
								<label for="permission_<?php echo $permission['permission_id']; ?>">
									<span class="permission-name"><?php echo htmlspecialchars($permission['permission_name']); ?></span>
								</label>
								: 
								<span class="permission-description"><?php echo htmlspecialchars($permission['description']); ?></span>
							</div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No permissions available.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Add Role</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('addRoleForm').onsubmit = function() {
            var checkboxes = document.querySelectorAll('input[name="permissions[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one permission.');
                return false;
            }
            return true;
        };
    </script>
</body>
</html>