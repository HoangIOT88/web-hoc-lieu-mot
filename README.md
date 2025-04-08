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
	•	Chọn menu “Danh sách khóa học”.
	•	Hệ thống hiển thị danh sách tất cả khóa học hiện có.
	•	User chọn khóa học để xem thông tin chi tiết (giáo trình, thời gian, giảng viên…).

2. Đăng ký khóa học
	•	User chọn khóa học từ danh sách khóa học.
	•	Nhấn nút “Đăng ký khóa học”.
	•	Hệ thống kiểm tra trạng thái đăng nhập:
	•	Chưa đăng nhập: Điều hướng sang màn hình đăng nhập.
	•	Đã đăng nhập: Xác nhận đăng ký và hiển thị kết quả (thành công hoặc thất bại).

3. Làm bài tập
	•	User đăng nhập.
	•	Truy cập mục “Khóa học của tôi”.
	•	Chọn khóa học đã đăng ký.
	•	Chọn mục “Danh sách bài tập”.
	•	Làm bài tập và nhấn nút “Nộp bài”.
	•	Hệ thống lưu bài làm và cập nhật trạng thái.

4. Đăng nhập
	•	User chọn mục “Đăng nhập”.
	•	Nhập thông tin tài khoản và mật khẩu.
	•	Hệ thống xác thực:
	•	Thành công: Điều hướng tới trang chủ.
	•	Thất bại: Hiển thị thông báo lỗi.

5. Chat nhóm
	•	User đăng nhập.
	•	Vào mục “Chat nhóm”.
	•	Chọn nhóm chat hoặc tạo cuộc trò chuyện mới.
	•	Gửi và nhận tin nhắn.

⸻

# B. Nhóm người dùng nội dung (Content User)

1. Tạo/Xóa khóa học
	•	Content User đăng nhập.
	•	Vào trang “Quản lý khóa học”.
	•	Tạo khóa học:
	•	Chọn “Thêm khóa học mới”.
	•	Nhập thông tin (tên, mô tả, thời gian học,…).
	•	Lưu khóa học.
	•	Xóa khóa học:
	•	Chọn khóa học cần xóa.
	•	Xác nhận xóa khóa học.
	•	Hệ thống cập nhật dữ liệu.

2. Tạo content và bài tập
	•	Chọn khóa học quản lý.
	•	Chọn “Thêm content và bài tập”.
	•	Nhập nội dung bài học, bài tập, deadline.
	•	Lưu nội dung vào hệ thống.

3. Tạo/Xóa nhóm chat
	•	Vào mục “Quản lý nhóm chat”.
	•	Tạo nhóm:
	•	Chọn “Tạo nhóm chat mới”.
	•	Nhập tên nhóm, mô tả, thêm thành viên.
	•	Xóa nhóm:
	•	Chọn nhóm cần xóa và xác nhận.

4. Duyệt User
	•	Chọn mục “Duyệt người dùng”.
	•	Hiển thị danh sách user đang chờ.
	•	Duyệt hoặc từ chối user.
	•	Cập nhật trạng thái.

5. Đăng bài giảng
	•	Chọn khóa học đang quản lý.
	•	Chọn mục “Đăng bài giảng”.
	•	Tải lên file bài giảng (pdf, video, bài viết,…).
	•	Xác nhận đăng tải.

6. Thêm User
	•	Chọn mục “Quản lý user”.
	•	Chọn “Thêm user mới”.
	•	Nhập thông tin (tên, email, quyền hạn…).
	•	Tạo user và thông báo kết quả.

⸻

# C. Nhóm Admin (Quản trị hệ thống)

1. Quản lý người dùng
	•	Admin đăng nhập.
	•	Chọn mục “Quản lý người dùng”.
	•	Xem toàn bộ danh sách người dùng.
	•	Thực hiện thêm/xóa/sửa thông tin user.
	•	Lưu thay đổi.

2. Thống kê người dùng
	•	Chọn mục “Thống kê người dùng”.
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

