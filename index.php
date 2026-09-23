<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['apk_file'])) {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadFile = $uploadDir . basename($_FILES['apk_file']['name']);

    if (move_uploaded_file($_FILES['apk_file']['tmp_name'], $uploadFile)) {
        // Run uber-apk-signer via shell command on Replit Linux server
        $command = "java -jar uber-apk-signer.jar --apk " . escapeshellarg($uploadFile) . " 2>&1";
        $output = shell_exec($command);

        // uber-apk-signer creates a signed apk usually in a 'out' folder or same folder with '-aligned-debugSigned.apk'
        // Let's find the signed apk file in the directory
        $dirFiles = scandir($uploadDir);
        $signedApkPath = "";
        foreach ($dirFiles as $file) {
            if (strpos($file, "-aligned-debugSigned.apk") !== false || strpos($file, "-signed.apk") !== false) {
                $signedApkPath = $uploadDir . $file;
                break;
            }
        }

        if ($signedApkPath && file_exists($signedApkPath)) {
            // Send back the signed APK file to the Android App
            header('Content-Type: application/vnd.android.package-archive');
            header('Content-Disposition: attachment; filename="' . basename($signedApkPath) . '"');
            readfile($signedApkPath);
            exit;
        } else {
            echo "Signing failed. Output: " . $output;
        }
    } else {
        echo "File upload failed.";
    }
} else {
    echo "C2 APK Signer Server is Active!";
}
?>
