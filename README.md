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

Mỗi thành viên chỉ làm việc trên branch của mình.

Ví dụ:

hongmy → Database
congxum → Admin
phuhuong → Client

---

## 2. Quy trình làm việc

### Bước 1

Chuyển sang branch cá nhân

bash
git checkout hongmy

### Bước 2

Lấy code mới nhất từ develop

bash
git pull origin develop

Hoặc:

bash
git fetch origin
git merge origin/develop

### Bước 3

Code chức năng được giao

### Bước 4

Commit

bash
git add .
git commit -m "[Database][Laravel] - Product: Tạo migration sản phẩm"

### Bước 5

Push lên branch cá nhân

bash
git push origin hongmy

### Bước 6

Tạo Pull Request

hongmy
↓
develop

Tuyệt đối không push trực tiếp lên:

main
develop

---

## 3. Quy tắc Commit

### Cấu trúc

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

---

## 4. Ví dụ Commit

### Database

[Database][Laravel] - Product: Tạo migration sản phẩm

[Database][Laravel] - Category: Tạo migration danh mục

[Database][Laravel] - Seeder: Thêm dữ liệu tỉnh thành

[Database][MySQL] - Order: Thêm khóa ngoại đơn hàng

### Admin

[Admin][PHP] - Product: Hiển thị số lượng tồn kho

[Admin][Filament] - User: Thêm chức năng khóa tài khoản

[Admin][Laravel] - Category: CRUD danh mục sản phẩm

### Client

[Client][HTML] - Home: Sửa giao diện trang chủ

[Client][CSS] - Product: Responsive trang chi tiết sản phẩm

[Client][JS] - Cart: Cập nhật số lượng sản phẩm

### Auth

[Auth][Laravel] - Login: Xử lý đăng nhập

[Auth][Laravel] - Register: Thêm xác thực email

### API

[API][Laravel] - Product: API lấy danh sách sản phẩm

[API][Laravel] - Order: API tạo đơn hàng

### Fix Bug

[Fix][PHP] - Cart: Sửa lỗi tính tổng tiền

[Fix][Laravel] - Order: Sửa lỗi tạo đơn hàng

---

## 5. Quy tắc Commit

### Nên

Mỗi commit chỉ giải quyết 1 chức năng.

Commit ngay sau khi hoàn thành một phần việc.

Tên commit phải đọc là hiểu ngay đã làm gì.

Ví dụ:

[Database][Laravel] - Product: Tạo migration sản phẩm

[Admin][PHP] - Product: Hiển thị số lượng tồn kho

### Không nên

update

fix

done

code mới

sửa lỗi

commit lần 2

---

## 6. Quy tắc Merge

Chỉ merge vào develop khi:

- Đã chạy project thành công.
- Không có lỗi migration.
- Không có conflict.
- Đã push branch cá nhân lên GitHub.

Sau khi tất cả chức năng hoàn thành:

Branch cá nhân
↓
develop
↓
main
