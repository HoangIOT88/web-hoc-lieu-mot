### Công nghệ
mysql  Ver 9.2.0 for macos15.2 on arm64 (Homebrew)
Composer version 2.8.5 2025-01-21 15:23:40
PHP version 8.2.28 (/opt/homebrew/Cellar/php@8.2/8.2.28/bin/php)
PHP 8.2.28 (cli) (built: Mar 11 2025 17:58:12) (NTS)
Laravel Version: 10.48.29

### Luồng hoạt động (Activity Flow)

## Giới thiệu

Tài liệu này mô tả chi tiết luồng hoạt động của hệ thống web, phân loại theo từng nhóm người dùng chính.

⸻

# A. Nhóm người dùng thông thường (User)

1. Xem các khóa học
	•	User truy cập vào trang web.
	•	Chọn menu "Danh sách khóa học".
	•	Hệ thống hiển thị danh sách tất cả khóa học hiện có.
	•	User chọn khóa học để xem thông tin chi tiết (giáo trình, thời gian, giảng viên…).

2. Đăng ký khóa học
	•	User chọn khóa học từ danh sách khóa học.
	•	Nhấn nút "Đăng ký khóa học".
	•	Hệ thống kiểm tra trạng thái đăng nhập:
	•	Chưa đăng nhập: Điều hướng sang màn hình đăng nhập.
	•	Đã đăng nhập: Xác nhận đăng ký và hiển thị kết quả (thành công hoặc thất bại).

3. Làm bài tập
	•	User đăng nhập.
	•	Truy cập mục "Khóa học của tôi".
	•	Chọn khóa học đã đăng ký.
	•	Chọn mục "Danh sách bài tập".
	•	Làm bài tập và nhấn nút "Nộp bài".
	•	Hệ thống lưu bài làm và cập nhật trạng thái.

4. Đăng nhập
	•	User chọn mục "Đăng nhập".
	•	Nhập thông tin tài khoản và mật khẩu.
	•	Hệ thống xác thực:
	•	Thành công: Điều hướng tới trang chủ.
	•	Thất bại: Hiển thị thông báo lỗi.

5. Chat nhóm
	•	User đăng nhập.
	•	Vào mục "Chat nhóm".
	•	Chọn nhóm chat hoặc tạo cuộc trò chuyện mới.
	•	Gửi và nhận tin nhắn.

⸻

# B. Nhóm người dùng nội dung (Content User)

1. Tạo/Xóa khóa học
	•	Content User đăng nhập.
	•	Vào trang "Quản lý khóa học".
	•	Tạo khóa học:
	•	Chọn "Thêm khóa học mới".
	•	Nhập thông tin (tên, mô tả, thời gian học,…).
	•	Lưu khóa học.
	•	Xóa khóa học:
	•	Chọn khóa học cần xóa.
	•	Xác nhận xóa khóa học.
	•	Hệ thống cập nhật dữ liệu.

2. Tạo content và bài tập
	•	Chọn khóa học quản lý.
	•	Chọn "Thêm content và bài tập".
	•	Nhập nội dung bài học, bài tập, deadline.
	•	Lưu nội dung vào hệ thống.

3. Tạo/Xóa nhóm chat
	•	Vào mục "Quản lý nhóm chat".
	•	Tạo nhóm:
	•	Chọn "Tạo nhóm chat mới".
	•	Nhập tên nhóm, mô tả, thêm thành viên.
	•	Xóa nhóm:
	•	Chọn nhóm cần xóa và xác nhận.

4. Duyệt User
	•	Chọn mục "Duyệt người dùng".
	•	Hiển thị danh sách user đang chờ.
	•	Duyệt hoặc từ chối user.
	•	Cập nhật trạng thái.

5. Đăng bài giảng
	•	Chọn khóa học đang quản lý.
	•	Chọn mục "Đăng bài giảng".
	•	Tải lên file bài giảng (pdf, video, bài viết,…).
	•	Xác nhận đăng tải.

6. Thêm User
	•	Chọn mục "Quản lý user".
	•	Chọn "Thêm user mới".
	•	Nhập thông tin (tên, email, quyền hạn…).
	•	Tạo user và thông báo kết quả.

⸻

# C. Nhóm Admin (Quản trị hệ thống)

1. Quản lý người dùng
	•	Admin đăng nhập.
	•	Chọn mục "Quản lý người dùng".
	•	Xem toàn bộ danh sách người dùng.
	•	Thực hiện thêm/xóa/sửa thông tin user.
	•	Lưu thay đổi.

2. Thống kê người dùng
	•	Chọn mục "Thống kê người dùng".
	•	Xem biểu đồ, thống kê số lượng và tình trạng hoạt động của user.
	•	Xuất báo cáo dạng Excel/PDF.

⸻

## Các Usecase tổng hợp

User thường (học viên)
	•	Xem danh sách khóa học → Chọn khóa học → Xem chi tiết.
	•	Đăng ký khóa học → Kiểm tra đăng nhập → Đăng ký → Thông báo.
	•	Làm bài tập → Đăng nhập → Chọn khóa học → Chọn bài tập → Nộp bài → Lưu kết quả.
	•	Đăng nhập → Nhập thông tin → Xác thực → Chuyển hướng hoặc thông báo lỗi.
	•	Chat nhóm → Đăng nhập → Chọn nhóm → Gửi/Nhận tin.

⸻

Content User (quản trị nội dung)
	•	Tạo khóa học mới → Nhập thông tin → Submit → Lưu.
	•	Xóa khóa học → Chọn khóa học → Xác nhận → Xóa.
	•	Tạo nội dung bài học và bài tập → Nhập nội dung → Submit → Lưu.
	•	Tạo nhóm chat → Nhập thông tin → Submit → Lưu.
	•	Xóa nhóm chat → Chọn nhóm → Xác nhận → Xóa.
	•	Duyệt user → Chọn user → Duyệt/Từ chối → Lưu trạng thái.
	•	Đăng bài giảng → Upload nội dung → Submit → Lưu.
	•	Thêm user → Nhập thông tin → Submit → Lưu.

⸻

Admin (quản trị hệ thống)
	•	Quản lý các user → Xem danh sách → Thêm/Xóa/Sửa → Lưu.
	•	Thống kê người dùng → Xem dữ liệu thống kê → Xuất báo cáo (pdf/excel).

⸻

## Thông tin khác
	•	Tài liệu này phục vụ việc thiết kế kỹ thuật, lập trình, và xây dựng hệ thống web.
	•	Các bước trình bày rõ ràng, chi tiết, thuận lợi cho việc triển khai.


### ERD Database Schema (Chi tiết) (plain text)
User
-----
id (PK)
name
email (unique)
password_hash
role (ENUM: USER, CONTENT_USER, ADMIN)
created_at
updated_at

Course
-----
id (PK)
name
description
duration
content_user_id (FK to User.id)
created_at
updated_at

CourseRegistration (ghi danh khóa học)
-----
id (PK)
user_id (FK to User.id)
course_id (FK to Course.id)
registered_at

Lecture (bài giảng của khóa học)
-----
id (PK)
course_id (FK to Course.id)
title
description
file_url
uploaded_at

Exercise (bài tập khóa học)
-----
id (PK)
course_id (FK to Course.id)
title
content
deadline
created_at

ExerciseSubmission (bài nộp của user)
-----
id (PK)
exercise_id (FK to Exercise.id)
user_id (FK to User.id)
submission_content
submitted_at
score
graded_at

ChatGroup
-----
id (PK)
name
description
created_by (FK to User.id)
created_at

ChatGroupMember
-----
id (PK)
group_id (FK to ChatGroup.id)
user_id (FK to User.id)
joined_at

Message
-----
id (PK)
group_id (FK to ChatGroup.id)
sender_id (FK to User.id)
content
sent_at

UserApproval (duyệt user)
-----
id (PK)
user_id (FK to User.id)
content_user_id (FK to User.id)
status (ENUM: PENDING, APPROVED, REJECTED)
reviewed_at

AdminReport (báo cáo admin)
-----
id (PK)
generated_by_admin_id (FK to User.id)
report_type (ENUM: USER_STATS, COURSE_STATS, ACTIVITY_STATS)
file_url
generated_at

## Các Relationship (quan hệ giữa các bảng):
	•	User (1) → (n) Course (content_user quản lý)
(Course.content_user_id → User.id)
	•	User (n) → (n) Course (user đăng ký khóa học)
(CourseRegistration làm bảng trung gian)
	•	Course (1) → (n) Lecture
(Lecture.course_id → Course.id)
	•	Course (1) → (n) Exercise
(Exercise.course_id → Course.id)
	•	Exercise (1) → (n) ExerciseSubmission
(ExerciseSubmission.exercise_id → Exercise.id)
	•	User (1) → (n) ExerciseSubmission
(ExerciseSubmission.user_id → User.id)
	•	ChatGroup (1) → (n) ChatGroupMember
(ChatGroupMember.group_id → ChatGroup.id)
	•	User (n) → (n) ChatGroup
(ChatGroupMember làm bảng trung gian)
	•	ChatGroup (1) → (n) Message
(Message.group_id → ChatGroup.id)
	•	User (1) → (n) Message
(Message.sender_id → User.id)
	•	User (1) → (n) UserApproval
(UserApproval.content_user_id → User.id: người duyệt)
(UserApproval.user_id → User.id: người được duyệt)
	•	User (Admin) (1) → (n) AdminReport
(AdminReport.generated_by_admin_id → User.id)


## Một số giải thích logic bổ sung:
	•	User.role: Phân biệt quyền người dùng (USER thường, CONTENT_USER, ADMIN).
	•	Bảng CourseRegistration: Lưu lịch sử user đăng ký khóa học.
	•	Bảng ExerciseSubmission: lưu nội dung bài nộp và trạng thái chấm điểm.
	•	Bảng ChatGroupMember: giúp quản lý thành viên từng nhóm chat.
	•	Bảng UserApproval: lưu trạng thái duyệt user, để xác định user nào đã được duyệt vào hệ thống.
	•	Bảng AdminReport: hỗ trợ Admin xuất các thống kê.

## Quan hệ thực tế ERD (tham khảo):
erDiagram
User ||--o{ Course : manages
User ||--o{ CourseRegistration : registers
Course ||--o{ CourseRegistration : registered_by
Course ||--o{ Lecture : has
Course ||--o{ Exercise : has
Exercise ||--o{ ExerciseSubmission : submitted_for
User ||--o{ ExerciseSubmission : submits
User ||--o{ ChatGroupMember : joins
ChatGroup ||--o{ ChatGroupMember : has_members
ChatGroup ||--o{ Message : contains
User ||--o{ Message : sends
User ||--o{ UserApproval : reviews
User ||--o{ UserApproval : being_reviewed
User ||--o{ AdminReport : generates

# Hướng dẫn cài đặt và chạy ứng dụng Quản Lý Bài Giảng

Dưới đây là hướng dẫn chi tiết để cài đặt và chạy ứng dụng trên 3 môi trường phổ biến, đảm bảo tính năng chat hoạt động đúng.

## Yêu cầu chung

- PHP 8.2+
- MySQL 9+
- Composer 2+
- Node.js và NPM
- Tài khoản Pusher (https://pusher.com)
- Git

## 1. Cài đặt trên Linux (Ubuntu/Debian)

### Bước 1: Cài đặt các công cụ cần thiết

```bash
# Cập nhật danh sách gói
sudo apt update

# Cài đặt PHP và các extension cần thiết
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-curl php8.2-mbstring php8.2-mysql php8.2-xml php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl

# Cài đặt MySQL
sudo apt install -y mysql-server

# Cài đặt Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Cài đặt Node.js và NPM qua NVM
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc
nvm install --lts
nvm use --lts
```

### Bước 2: Cài đặt và cấu hình ứng dụng

```bash
# Clone repository
git clone <URL_REPOSITORY> quan-ly-bai-giang
cd quan-ly-bai-giang

# Cài đặt các dependency PHP
composer install

# Cài đặt các dependency JavaScript
npm install

# Tạo file môi trường
cp .env.example .env

# Tạo khóa ứng dụng
php artisan key:generate

# Cập nhật file .env với thông tin database
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=quan_ly_bai_giang
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Tạo database
sudo mysql -e "CREATE DATABASE quan_ly_bai_giang;"

# Chạy migration và seeding
php artisan migrate --seed
```

### Bước 3: Cấu hình Pusher cho chat

```bash
# Đăng ký tài khoản tại https://pusher.com
# Tạo một ứng dụng mới và lấy thông tin App ID, Key, Secret và Cluster

# Cập nhật file .env với thông tin Pusher
# BROADCAST_DRIVER=pusher
# PUSHER_APP_ID=your_app_id
# PUSHER_APP_KEY=your_app_key
# PUSHER_APP_SECRET=your_app_secret
# PUSHER_APP_CLUSTER=your_app_cluster
```

### Bước 4: Khởi chạy ứng dụng

```bash
# Compile assets
npm run build

# Khởi động máy chủ
php artisan serve

# Mở trình duyệt và truy cập http://localhost:8000
```

## 2. Cài đặt trên Windows (XAMPP)

### Bước 1: Cài đặt XAMPP

1. Tải XAMPP phiên bản PHP 8.2+ từ [trang chủ XAMPP](https://www.apachefriends.org/download.html)
2. Cài đặt XAMPP theo hướng dẫn
3. Khởi động Apache và MySQL từ XAMPP Control Panel

### Bước 2: Cài đặt Composer và Node.js

1. Tải và cài đặt Composer từ [getcomposer.org](https://getcomposer.org/download/)
2. Tải và cài đặt Node.js từ [nodejs.org](https://nodejs.org/) (chọn phiên bản LTS)
3. Khởi động lại máy tính để đảm bảo biến môi trường được cập nhật

### Bước 3: Cài đặt ứng dụng

```cmd
# Clone repository vào thư mục htdocs của XAMPP
cd C:\xampp\htdocs
git clone <URL_REPOSITORY> quan-ly-bai-giang
cd quan-ly-bai-giang

# Cài đặt các dependency PHP
composer install

# Cài đặt các dependency JavaScript
npm install

# Tạo file môi trường
copy .env.example .env

# Tạo khóa ứng dụng
php artisan key:generate
```

### Bước 4: Cấu hình cơ sở dữ liệu

1. Mở phpMyAdmin từ XAMPP Control Panel (http://localhost/phpmyadmin)
2. Tạo database mới với tên `quan_ly_bai_giang`
3. Cập nhật file `.env` với thông tin database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quan_ly_bai_giang
DB_USERNAME=root
DB_PASSWORD=
```

4. Chạy migration và seeding:
```cmd
php artisan migrate --seed
```

### Bước 5: Cấu hình Pusher

1. Đăng ký tài khoản tại [Pusher](https://pusher.com)
2. Tạo một ứng dụng mới và lấy thông tin App ID, Key, Secret và Cluster
3. Cập nhật file `.env` với thông tin Pusher:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_app_cluster
```

### Bước 6: Khởi chạy ứng dụng

```cmd
# Compile assets
npm run build

# Tạo symbolic link cho storage
php artisan storage:link

# Xóa cache
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize

# Khởi động máy chủ hoặc sử dụng Apache trong XAMPP
php artisan serve
```

## 3. Cài đặt trên macOS (Homebrew)

### Bước 1: Cài đặt các công cụ cần thiết

```bash
# Cài đặt Homebrew nếu chưa có
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Cài đặt PHP 8.2
brew install php@8.2
brew link php@8.2

# Cài đặt MySQL
brew install mysql
brew services start mysql

# Cài đặt Composer
brew install composer

# Cài đặt Node.js và NPM
brew install node
```

### Bước 2: Cài đặt và cấu hình ứng dụng

```bash
# Clone repository
git clone <URL_REPOSITORY> quan-ly-bai-giang
cd quan-ly-bai-giang

# Cài đặt các dependency PHP
composer install

# Cài đặt các dependency JavaScript
npm install

# Tạo file môi trường
cp .env.example .env

# Tạo khóa ứng dụng
php artisan key:generate

# Tạo database
mysql -u root -p -e "CREATE DATABASE quan_ly_bai_giang;"

# Cập nhật file .env với thông tin database
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=quan_ly_bai_giang
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Chạy migration và seeding
php artisan migrate --seed
```

### Bước 3: Cấu hình Pusher cho chat

```bash
# Đăng ký tài khoản tại https://pusher.com
# Tạo một ứng dụng mới và lấy thông tin App ID, Key, Secret và Cluster

# Cập nhật file .env với thông tin Pusher
# BROADCAST_DRIVER=pusher
# PUSHER_APP_ID=your_app_id
# PUSHER_APP_KEY=your_app_key
# PUSHER_APP_SECRET=your_app_secret
# PUSHER_APP_CLUSTER=your_app_cluster
```

### Bước 4: Khởi chạy ứng dụng

```bash
# Compile assets
npm run build

# Tạo symbolic link cho storage
php artisan storage:link

# Xóa cache
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize

# Khởi động máy chủ
php artisan serve

# Mở trình duyệt và truy cập http://localhost:8000
```

## Xử lý sự cố với tính năng chat

Nếu tính năng chat không hoạt động, hãy kiểm tra các điểm sau:

1. **Kiểm tra console của trình duyệt** để xem có lỗi liên quan đến Pusher hay không

2. **Đảm bảo các thông tin Pusher trong file `.env` chính xác**:
   ```
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_APP_CLUSTER=your_app_cluster
   ```

3. **Đảm bảo BroadcastServiceProvider được kích hoạt** trong `config/app.php`:
   ```php
   App\Providers\BroadcastServiceProvider::class,
   ```

4. **Kiểm tra định nghĩa kênh** trong `routes/channels.php`:
   ```php
   Broadcast::channel('chat-group.{groupId}', function ($user, $groupId) {
       return \DB::table('chat_group_members')
           ->where('group_id', $groupId)
           ->where('user_id', $user->id)
           ->exists();
   });
   ```

5. **Xóa cache sau mỗi thay đổi**:
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan optimize
   ```

## Cấu trúc Database và Mối quan hệ

Ứng dụng sử dụng các bảng sau cho tính năng chat:

1. `chat_groups`: Lưu thông tin về các nhóm chat
2. `chat_group_members`: Bảng trung gian lưu thành viên của mỗi nhóm chat
3. `messages`: Lưu tin nhắn của các nhóm chat

## Tài khoản mẫu sau khi seeding

- **Admin**:
  - Email: admin@example.com
  - Password: password

- **Content User**:
  - Email: content@example.com
  - Password: password

- **Regular User**:
  - Email: user@example.com
  - Password: password

