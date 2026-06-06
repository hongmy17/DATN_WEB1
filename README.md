# Git Flow & Quy tắc Commit

## 1. Git Flow của nhóm

### Các nhánh chính

main → Phiên bản ổn định
develop → Nhánh tổng hợp code của cả nhóm

### Nhánh cá nhân

hongmy
congxum
phuhuong
tttinh
dinhxuancuong

Thành viên chỉ làm việc trên branch của mình.

## 2. Quy trình làm việc

### Bước 1

Chuyển sang branch cá nhân

### Bước 2

Lấy code mới nhất từ develop

### Bước 3

Code chức năng được giao

### Bước 4

Commit

### Bước 5

Push lên branch cá nhân

## 3. Quy tắc Commit

### Cấu trúc commit

[Module][Technology] - Feature: Nội dung thay đổi

### Module

Admin
Client
Database
Auth
API
System
Fix

### Technology

PHP
Laravel
HTML
CSS
JS
Filament
MySQL

### Feature

Product
Category
Order
User
Home
Cart
Checkout
Payment
Dashboard


## Mỗi commit chỉ giải quyết 1 chức năng.

## Commit ngay sau khi hoàn thành một phần việc.

## Tên commit phải đọc là hiểu ngay đã làm gì.

Ví dụ:

[Database][Laravel] - Product: Tạo migration sản phẩm

[Admin][PHP] - Product: Hiển thị số lượng tồn kho
