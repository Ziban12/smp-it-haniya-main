# Database Schema Attributes for CRUD Implementation

## Folder Structure Required

```
app/Http/Controllers/Employee/
├── EmployeeController.php (DONE)
├── TeacherController.php (DONE)
├── StudentController.php (DONE)
├── ClassController.php
├── SubjectController.php
├── AcademicYearController.php
├── ArticleController.php
├── EventController.php
├── SettingController.php
├── TagArticleController.php
├── TagEventController.php
└── GradeController.php

resources/views/
├── employees/ (index, create, edit forms)
├── teachers/ (index, create, edit forms)
├── students/ (index, create, edit forms)
├── classes/ (index, create, edit forms)
├── subjects/ (index, create, edit forms)
├── academic_years/ (index, create, edit forms)
├── articles/ (index, create, edit forms)
├── events/ (index, create, edit forms)
├── settings/ (index, create, edit forms)
├── tag_articles/ (index, create, edit forms)
├── tag_events/ (index, create, edit forms)
└── grades/ (index, create, edit forms)
```

## Model Attributes

### MstClass
- class_id (Primary Key, string)
- class_name (string, max:100, required)
- class_level (string, max:50, required)
- homeroom_teacher_id (string, nullable)
- created_at, updated_at, created_by, updated_by

### MstSubject
- subject_id (Primary Key, auto-increment, int)
- subject_name (string, max:100, required)
- subject_code (string, max:50, required, unique)
- class_level (string, max:50, required)
- description (text, nullable)
- created_at, updated_at, created_by, updated_by

### MstAcademicYear
- academic_year_id (Primary Key, string)
- start_date (date, required)
- end_date (date, required)
- semester (string, max:10, required)
- status (enum: Active/Inactive, required)
- created_at, updated_at, created_by, updated_by

### MstArticle
- article_id (Primary Key, auto-increment, int)
- title (string, max:255, required)
- slug (string, max:255, required, unique)
- content (longtext, required)
- image (string, max:255, nullable)
- article_type (string, max:50, required)
- status (enum: Active/Inactive, required)
- created_at, updated_at, created_by, updated_by

### MstEvent
- event_id (Primary Key, auto-increment, int)
- event_name (string, max:255, required)
- description (longtext, nullable)
- location (string, max:255, nullable)
- status (enum: Active/Inactive, required)
- created_at, updated_at, created_by, updated_by

### MstHeaderSetting
- header_id (Primary Key, auto-increment, int)
- title (string, max:255, required)
- created_at, updated_at, created_by, updated_by

### MstDetailSetting
- detail_id (Primary Key, auto-increment, int)
- header_id (int, required, foreign key)
- item_code (string, max:50, required)
- item_name (string, max:255, required)
- item_desc (text, nullable)
- status (enum: Active/Inactive, required)
- item_type (string, max:50, nullable)
- created_at, updated_at, created_by, updated_by

### MstTagArticle
- tag_id (Primary Key, auto-increment, int)
- article_id (int, required, foreign key)
- tag_code (string, max:50, required)
- created_at, updated_at, created_by, updated_by

### MstTagEvent
- tag_id (Primary Key, auto-increment, int)
- event_id (int, required, foreign key)
- tag_code (string, max:50, required)
- created_at, updated_at, created_by, updated_by

### TxnGrade
- grade_id (Primary Key, auto-increment, int)
- student_id (string, required, foreign key)
- subject_id (int, required, foreign key)
- academic_year_id (string, required, foreign key)
- score (decimal:5,2, required)
- status (enum: Active/Inactive, required)
- created_at, updated_at, created_by, updated_by
