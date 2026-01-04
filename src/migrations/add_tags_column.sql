-- Migration: Add tags column to article table
-- Run this SQL in your MySQL/phpMyAdmin

ALTER TABLE article ADD COLUMN tags VARCHAR(255) DEFAULT NULL;
