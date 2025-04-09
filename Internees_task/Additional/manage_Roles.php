<?php
include 'db_connection.php';
include "header.php";
// Function to add a new role
function addRole($roleName, $description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $roleName, $description);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Function to save permissions
function savePermissions($roleId, $permissions) {
    global $conn;
    
    // First, delete existing permissions for this role
    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $stmt->close();
    
    // Then, insert new permissions
    $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
    foreach ($permissions as $permissionId) {
        $stmt->bind_param("ii", $roleId, $permissionId);
        $stmt->execute();
    }
    $stmt->close();
    
    return true;
}

// Function to load existing permissions for a role
function loadPermissions($roleId) {
    global $conn;
    $permissions = array();
    
    $stmt = $conn->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row['permission_id'];
    }
    
    $stmt->close();
    return $permissions;
}

// Function to get all roles
function getAllRoles() {
    global $conn;
    $roles = array();
    
    $result = $conn->query("SELECT role_id, role_name FROM roles");
    
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
    
    return $roles;
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_role'])) {
        $roleName = $_POST['role_name'];
        $description = $_POST['role_description'];
        if (addRole($roleName, $description)) {
            $message = "New role added successfully.";
        } else {
            $error = "Error adding new role.";
        }
    } elseif (isset($_POST['save_permissions'])) {
        $roleId = $_POST['role_id'];
        $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : array();
        if (savePermissions($roleId, $permissions)) {
            $message = "Permissions saved successfully.";
        } else {
            $error = "Error saving permissions.";
        }
    }
}

// Get all roles for the dropdown
$roles = getAllRoles();

// Get selected role's permissions
$selectedRoleId = isset($_GET['role_id']) ? $_GET['role_id'] : (isset($roles[0]) ? $roles[0]['role_id'] : null);
$selectedRolePermissions = $selectedRoleId ? loadPermissions($selectedRoleId) : array();

// Get all permissions
$allPermissions = array();
$result = $conn->query("SELECT permission_id, permission_name, description FROM permissions");
while ($row = $result->fetch_assoc()) {
    $allPermissions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIRDA Knowledge Hub - Manage Roles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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
            width: 95%;
            max-width: 900px;
            margin: auto;
            overflow: visible;
            padding: 0 20px;
        }

        header {
            background: var(--secondary-color);
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            text-align: center;
            font-weight: 600;
        }

        .content {
            background: #fff;
            padding: 2rem;
            margin-top: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
		.tpbutton {
            display: inline-block;
            background: var(--primary-color);
            color: #ffffff;
            padding: 10px 20px;
            margin: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
			float: right; 
			padding-bottom: 10px;
        }
        .button {
            display: inline-block;
            background: var(--primary-color);
            color: #ffffff;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .button:hover {
            background: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 2px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .permission-toggle {
            width: 42px;
            height: 10px;
            background: #1a237e;
            border-radius: 30px;
            padding: 4px;
            transition: all 300ms ease-in-out;
        }

        .permission-toggle > input {
            display: none;
        }

        .permission-toggle > label {
            display: block;
            width: 15px;
            height: 15px;
            cursor: pointer;
            background: #fff;
            border-radius: 50%;
            transition: all 300ms ease-in-out;
            margin-top: -2px;
        }

        .permission-toggle > input:checked + label {
            margin-left: 30px;
        }

        .permission-toggle > input:checked ~ .background {
            background: var(--primary-color);
        }

        .section-header {
            background-color: #f0f0f0;
            font-weight: bold;
        }
		/* Add New Role section */
		.add-role-form {
			margin-bottom: 0px;
			padding: 2px;
			background-color: #f8f9fa;
			border-radius: 8px;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
			display: flex;
			align-items: center;
			gap: 20px;
		}

		.add-role-form input[type="text"] {
			flex: 1;
			padding: 8px;
			border: 1px solid #ced4da;
			border-radius: 4px;
			font-size: 14px;
		}

		.add-role-form input[type="text"]:focus {
			border-color: var(--primary-color);
			outline: 0;
			box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);
		}

		.add-role-form .button {
			flex-shrink: 1;
		}

		/* Role Selector */
		.role-selector {
			margin-bottom: 0px;
			display: flex;
			align-items: center;
			gap: 0px;
		}
		
		.role-selector select {
			padding: 8px 10px;
			font-size: 14px;
			border: 1px solid #ced4da;
			border-radius: 4px;
			background-color: #fff;
			color: var(--text-color);
			width: 200px;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		}

		.role-selector select:focus {
			border-color: var(--primary-color);
			outline: 0;
			box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);
		}

		.role-selector label {
			font-weight: bold;
			margin-right: 10px;
		}
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Role's Permission Management</h2>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <h3>Add New Role</h3>
				<div class="add-role-form">
					<form method="POST" action="" class="add-role-form">
						<input type="text" name="role_name" placeholder="Role Name" required>
						<input type="text" name="role_description" placeholder="Role Description">
						<button type="submit" name="add_role" class="button"><i class="fas fa-plus"></i> Add Role</button>
					</form>
				</div>
            
            <h3>Manage Role Permissions</h3>
				<div class="role-selector">
					<label for="role-select">Select Role:</label>
					<form method="GET" action="">
						<select id="role-select" name="role_id" onchange="this.form.submit()">
							<?php foreach ($roles as $role): ?>
								<option value="<?php echo $role['role_id']; ?>" <?php echo $selectedRoleId == $role['role_id'] ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($role['role_name']); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</form>
				</div>
            
            <form method="POST" action="">
                <input type="hidden" name="role_id" value="<?php echo $selectedRoleId; ?>">
				<button type="submit" name="save_permissions" class="tpbutton"><i class="fas fa-save"></i> Save Permissions</button>
                <table id="permissionsTable">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Description</th>
                            <th>Granted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allPermissions as $permission): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($permission['permission_name']); ?></td>
                                <td><?php echo htmlspecialchars($permission['description']); ?></td>
                                <td>
                                    <div class="permission-toggle">
                                        <input type="checkbox" id="permission-<?php echo $permission['permission_id']; ?>" 
                                               name="permissions[]" value="<?php echo $permission['permission_id']; ?>"
                                               <?php echo in_array($permission['permission_id'], $selectedRolePermissions) ? 'checked' : ''; ?>>
                                        <label for="permission-<?php echo $permission['permission_id']; ?>"></label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="save_permissions" class="button">Save Permissions</button>
            </form>
        </div>
    </div>

    <script>
        // You can add any necessary JavaScript here
    </script>
</body>
</html>