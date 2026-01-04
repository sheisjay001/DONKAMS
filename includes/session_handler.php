<?php
class DBSessionHandler implements SessionHandlerInterface {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        // Ensure the sessions table exists to prevent crashes on fresh deployments
        $this->ensureTable();
    }

    private function ensureTable() {
        try {
            // Attempt to create the table if it doesn't exist.
            // Using CREATE TABLE IF NOT EXISTS is generally safe and low-overhead for this scale.
            $this->conn->query("CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(128) NOT NULL PRIMARY KEY,
                access INT(10) UNSIGNED,
                data TEXT
            )");
        } catch (Exception $e) {
            // If creation fails (e.g. permissions), we log it but continue 
            // hoping the table exists. If not, read/write will throw later.
            error_log("Session table creation failed: " . $e->getMessage());
        }
    }

    public function open(string $path, string $name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $id): string|false {
        $stmt = $this->conn->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['data'];
            }
        }
        return "";
    }

    public function write(string $id, string $data): bool {
        $access = time();
        $stmt = $this->conn->prepare("REPLACE INTO sessions (id, access, data) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $id, $access, $data);
        return $stmt->execute();
    }

    public function destroy(string $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function gc(int $maxlifetime): int|false {
        $old = time() - $maxlifetime;
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE access < ?");
        $stmt->bind_param("i", $old);
        if ($stmt->execute()) {
            return $stmt->affected_rows;
        }
        return false;
    }
}

