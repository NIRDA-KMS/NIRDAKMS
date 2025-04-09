<?php
include 'db_connection.php';
include "header.php";
// Function to get all roles
function getRoles($conn) {
    $sql = "SELECT role_id, role_name FROM roles";
    $result = $conn->query($sql);
    $roles = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
    }
    return $roles;
}

// Function to get permissions for a role
function getPermissionsForRole($conn, $roleId) {
    $sql = "SELECT p.permission_id, p.permission_name, p.description, 
            CASE WHEN rp.role_id IS NOT NULL THEN 1 ELSE 0 END AS is_granted
            FROM permissions p
            LEFT JOIN role_permissions rp ON p.permission_id = rp.permission_id AND rp.role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row;
    }
    return $permissions;
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addPermission':
                $newPermission = $_POST['newPermission'];
                $description = $_POST['description'];
                $sql = "INSERT INTO permissions (permission_name, description) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $newPermission, $description);
                $stmt->execute();
                break;
            
            case 'savePermissions':
                $roleId = $_POST['roleId'];
                $permissions = $_POST['permissions'];
                
                // First, remove all existing permissions for this role
                $sql = "DELETE FROM role_permissions WHERE role_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $roleId);
                $stmt->execute();
                
                // Then, add the new permissions
                $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                foreach ($permissions as $permissionId) {
                    $stmt->bind_param("ii", $roleId, $permissionId);
                    $stmt->execute();
                }
                break;
            
            case 'editPermission':
                $permissionId = $_POST['permissionId'];
                $newName = $_POST['newName'];
                $newDescription = $_POST['newDescription'];
                $sql = "UPDATE permissions SET permission_name = ?, description = ? WHERE permission_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $newName, $newDescription, $permissionId);
                $stmt->execute();
                break;
            
            case 'deletePermission':
                $permissionId = $_POST['permissionId'];
                $sql = "DELETE FROM permissions WHERE permission_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $permissionId);
                $stmt->execute();
                break;
        }
    }
}

$roles = getRoles($conn);
$currentRoleId = isset($_GET['role']) ? $_GET['role'] : $roles[0]['role_id'];
$permissions = getPermissionsForRole($conn, $currentRoleId);

?>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
	 <link href="css/style.css" rel="stylesheet">
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
		.role-selector {
			margin: 20px 0;
			padding: 15px;
			background-color: #f8f9fa;
			border-radius: 5px;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}

		.role-selector label {
			font-weight: 600;
			margin-right: 10px;
			color: var(--secondary-color);
		}

		#roleSelect {
			padding: 8px 10px;
			font-size: 14px;
			border: 1px solid #ced4da;
			border-radius: 4px;
			background-color: #fff;
			color: var(--text-color);
			width: 200px;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		}

		#roleSelect:focus {
			border-color: var(--primary-color);
			outline: 0;
			box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);
		}

		#roleSelect option {
			padding: 8px;
		}
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <div class="container">
        <div class="content">
            <h2>Permission Management</h2>
            <p>Manage user role permissions for different sections of the system.</p>
            
            <div class="action-buttons">
                <button class="button" onclick="addPermission()"><i class="fas fa-plus"></i> Add New Permission</button>
                <button class="button" onclick="savePermissions()"><i class="fas fa-save"></i> Save Changes</button>
            </div>

            <div class="role-selector">
                <label for="roleSelect">Select Role:</label>
                <select id="roleSelect" onchange="changeRole(this.value)">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['role_id']; ?>" <?php echo $role['role_id'] == $currentRoleId ? 'selected' : ''; ?>>
                            <?php echo $role['role_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <table id="permissionsTable">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Description</th>
                        <th>Granted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissions as $permission): ?>
                        <tr>
                            <td><?php echo $permission['permission_name']; ?></td>
                            <td><?php echo $permission['description']; ?></td>
                            <td>
                                <div class='permission-toggle'>
                                    <input type='checkbox' id='<?php echo $permission['permission_id']; ?>' <?php echo $permission['is_granted'] ? 'checked' : ''; ?>>
                                    <label for='<?php echo $permission['permission_id']; ?>'></label>
                                </div>
                            </td>
                            <td>
                                <button onclick="editPermission(<?php echo $permission['permission_id']; ?>, '<?php echo addslashes($permission['permission_name']); ?>', '<?php echo addslashes($permission['description']); ?>')">Edit</button>
                                <button onclick="deletePermission(<?php echo $permission['permission_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function addPermission() {
            var newPermission = prompt("Enter the name of the new permission:");
            if (newPermission) {
                var description = prompt("Enter a description for the new permission:");
                $.post('manage_permissions.php', {
                    action: 'addPermission',
                    newPermission: newPermission,
                    description: description
                }, function(response) {
                    location.reload();
                });
            }
        }

        function savePermissions() {
            var permissions = [];
            $('.permission-toggle input:checked').each(function() {
                permissions.push($(this).attr('id'));
            });

            $.post('manage_permissions.php', {
                action: 'savePermissions',
                roleId: $('#roleSelect').val(),
                permissions: permissions
            }, function(response) {
                alert('Permissions saved successfully!');
            });
        }

        function changeRole(roleId) {
            window.location.href = 'manage_permissions.php?role=' + roleId;
        }

        function editPermission(permissionId, permissionName, description) {
            var newName = prompt("Enter the new name for this permission:", permissionName);
            if (newName !== null) {
                var newDescription = prompt("Enter the new description for this permission:", description);
                if (newDescription !== null) {
                    $.post('manage_permissions.php', {
                        action: 'editPermission',
                        permissionId: permissionId,
                        newName: newName,
                        newDescription: newDescription
                    }, function(response) {
                        location.reload();
                    });
                }
            }
        }

        function deletePermission(permissionId) {
            if (confirm("Are you sure you want to delete this permission?")) {
                $.post('manage_permissions.php', {
                    action: 'deletePermission',
                    permissionId: permissionId
                }, function(response) {
                    location.reload();
                });
            }
        }
    </script>
</body>
</html>