-- Add role column to users table
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER password;

-- Create index for faster role queries
CREATE INDEX idx_users_role ON users(role);
