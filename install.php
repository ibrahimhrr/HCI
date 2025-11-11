<?php
/**
 * OnlyPlans Installation Wizard
 * This script will automatically set up your database and configuration
 */

session_start();
$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Test database connection
        $host = $_POST['db_host'];
        $username = $_POST['db_username'];
        $password = $_POST['db_password'];
        $database = $_POST['db_name'];
        
        $test_conn = @mysqli_connect($host, $username, $password);
        
        if (!$test_conn) {
            $error = "Cannot connect to MySQL server. Please check your credentials.";
        } else {
            // Check if database exists, create if not
            $db_exists = mysqli_select_db($test_conn, $database);
            
            if (!$db_exists) {
                $create_db = mysqli_query($test_conn, "CREATE DATABASE `$database`");
                if (!$create_db) {
                    $error = "Cannot create database. Please ensure your user has CREATE DATABASE privileges.";
                } else {
                    mysqli_select_db($test_conn, $database);
                }
            }
            
            if (!$error) {
                // Save credentials to session for next step
                $_SESSION['db_host'] = $host;
                $_SESSION['db_username'] = $username;
                $_SESSION['db_password'] = $password;
                $_SESSION['db_name'] = $database;
                
                // Create connection.php file
                $connection_content = "<?php\n";
                $connection_content .= "\$hostname = '$host';\n";
                $connection_content .= "\$username = '$username';\n";
                $connection_content .= "\$password = '$password';\n";
                $connection_content .= "\$database = '$database';\n";
                $connection_content .= "\$connection = mysqli_connect(\$hostname, \$username, \$password, \$database);\n\n";
                $connection_content .= "if (!(\$connection)) {\n";
                $connection_content .= "    echo \"Database Connection Failed\";\n";
                $connection_content .= "}\n";
                $connection_content .= "?>";
                
                file_put_contents('connection.php', $connection_content);
                
                mysqli_close($test_conn);
                header('Location: install.php?step=2');
                exit;
            }
            
            mysqli_close($test_conn);
        }
    } elseif ($step == 2) {
        // Create database tables
        include('connection.php');
        
        // Read and execute SQL file
        $sql_file = file_get_contents('database_setup.sql');
        
        // Split into individual queries
        $queries = array_filter(array_map('trim', explode(';', $sql_file)));
        
        $all_success = true;
        foreach ($queries as $query) {
            if (!empty($query)) {
                $result = mysqli_query($connection, $query);
                if (!$result) {
                    $all_success = false;
                    $error = "Error creating tables: " . mysqli_error($connection);
                    break;
                }
            }
        }
        
        if ($all_success) {
            mysqli_close($connection);
            header('Location: install.php?step=3');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OnlyPlans Installation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .install-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .install-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .install-header h1 {
            color: #2c3e50;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .install-header p {
            color: #666;
            font-size: 16px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        .step::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #ddd;
        }
        .step.active::after {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .step.completed::after {
            background: #28a745;
        }
        .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 50%;
            background: #ddd;
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .step.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .step.completed .step-number {
            background: #28a745;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #ddd;
            padding: 12px;
        }
        .btn-install {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 16px;
        }
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .alert {
            border-radius: 8px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f8f9fb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box h4 {
            color: #2c3e50;
            margin-top: 0;
        }
        .info-box code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            color: #e83e8c;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1>📅 OnlyPlans</h1>
            <p>Smart Calendar Installation Wizard</p>
        </div>

        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                <span class="step-number">1</span>
                <div>Database</div>
            </div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                <span class="step-number">2</span>
                <div>Setup</div>
            </div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">
                <span class="step-number">3</span>
                <div>Complete</div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <h3>Step 1: Database Configuration</h3>
            <p style="color: #666; margin-bottom: 20px;">Enter your MySQL database credentials. The installer will create the database if it doesn't exist.</p>

            <div class="info-box">
                <h4>🔍 Where to find these?</h4>
                <p><strong>Using MAMP/XAMPP/WAMP:</strong></p>
                <ul>
                    <li><strong>Host:</strong> Usually <code>localhost</code></li>
                    <li><strong>Username:</strong> Usually <code>root</code></li>
                    <li><strong>Password:</strong> Usually <code>root</code> (MAMP) or empty (XAMPP)</li>
                    <li><strong>Database:</strong> Choose any name like <code>onlyplans</code></li>
                </ul>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="localhost" required>
                </div>

                <div class="form-group">
                    <label>Database Username</label>
                    <input type="text" name="db_username" class="form-control" value="root" required>
                </div>

                <div class="form-group">
                    <label>Database Password</label>
                    <input type="password" name="db_password" class="form-control" placeholder="Leave empty if no password">
                </div>

                <div class="form-group">
                    <label>Database Name</label>
                    <input type="text" name="db_name" class="form-control" value="onlyplans" required>
                    <small class="text-muted">Will be created automatically if it doesn't exist</small>
                </div>

                <button type="submit" class="btn-install">Continue to Setup →</button>
            </form>

        <?php elseif ($step == 2): ?>
            <h3>Step 2: Creating Database Tables</h3>
            <p style="color: #666; margin-bottom: 20px;">Click the button below to create the necessary database tables.</p>

            <div class="info-box">
                <h4>📊 What will be created?</h4>
                <ul>
                    <li><strong>table_event:</strong> Stores all your calendar events</li>
                    <li>Includes fields for title, dates, times, and color coding</li>
                    <li>Optimized for Smart Suggestions feature</li>
                </ul>
            </div>

            <form method="POST">
                <button type="submit" class="btn-install">Create Database Tables →</button>
            </form>

        <?php elseif ($step == 3): ?>
            <div class="success-icon">✅</div>
            <h3 style="text-align: center; color: #28a745; margin-bottom: 20px;">Installation Complete!</h3>
            
            <div class="info-box">
                <h4>🎉 Your calendar is ready to use!</h4>
                <p><strong>What's next?</strong></p>
                <ul>
                    <li>✓ Database configured and connected</li>
                    <li>✓ Tables created successfully</li>
                    <li>✓ Smart Suggestions enabled</li>
                </ul>
            </div>

            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                <strong>🔒 Security Note:</strong> For security reasons, please delete <code>install.php</code> after installation is complete.
            </div>

            <a href="index.php" class="btn-install" style="display: block; text-align: center; text-decoration: none; line-height: 1.5;">
                Launch OnlyPlans Calendar →
            </a>

            <p style="text-align: center; margin-top: 20px; color: #666;">
                Need help? Check out <a href="README.md" style="color: #667eea;">README.md</a> for usage guide
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
