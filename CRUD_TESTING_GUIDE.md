# CRUD Testing Guide - Complete Instructions

## Server Setup

1. **Start PHP Server**
```bash
cd c:\xampp\htdocs\smp-it-haniya-main
php artisan serve --host=127.0.0.1 --port=8081
```

2. **Access Application**
```
URL: http://127.0.0.1:8081/employee/dashboard
```

---

## Test Scenario 1: STUDENT CRUD

### 1a. CREATE (C)
**URL**: `http://127.0.0.1:8081/employee/students/create`

**Steps**:
1. Click "Add New Student" button
2. Fill form:
   - First Name: `John`
   - Last Name: `Doe`
   - NIS: `001001`
   - Password: `password123`
   - Confirm Password: `password123`
   - Gender: `Male` (optional)
   - Birth Date: `2010-01-15` (optional)
   - **Profile Photo**: Select image file (optional, JPG/PNG, max 2MB)
   - Status: `Active`
3. Click "Create Student"

**Expected Result**:
- ✅ New student created with auto-generated student_id
- ✅ Profile photo stored in `storage/app/public/students/`
- ✅ Redirect to student list with success message
- ✅ **Student ID displayed in table** (visible in list)

---

### 1b. READ (R)
**URL**: `http://127.0.0.1:8081/employee/students`

**Expected Result**:
- ✅ Table displays all active students
- ✅ **Student ID column shows auto-generated IDs** (e.g., 1, 2, 3...)
- ✅ Can see: Full Name, NIS, Gender, Father Name, Entry Date
- ✅ Edit and Delete buttons visible for each row

---

### 1c. UPDATE (U)
**URL**: `http://127.0.0.1:8081/employee/students/{student_id}/edit`

**Steps**:
1. Click "Edit" button on any student
2. Modify data:
   - Change First Name to: `Jane`
   - **Upload NEW Profile Photo** (optional)
3. Click "Update Student"

**Expected Result**:
- ✅ Student record updated successfully
- ✅ New profile photo uploaded and replaced (if provided)
- ✅ Redirect to list with success message
- ✅ **Student ID remains the same** in the table

---

### 1d. DELETE (D)
**URL**: From student list, click Delete button

**Steps**:
1. Click "Delete" button on any student
2. Confirm deletion in popup

**Expected Result**:
- ✅ Student marked as Inactive
- ✅ Removed from active students list
- ✅ Redirect to list with success message
- ✅ **Student ID no longer displayed** (filtered out)

---

## Test Scenario 2: EMPLOYEE CRUD

### 2a. CREATE
**URL**: `http://127.0.0.1:8081/employee/employees/create`

**Steps**:
1. Fill form:
   - First Name: `Ahmad`
   - Last Name: `Rahman`
   - Username: `ahmad.rahman`
   - Email: `ahmad@school.com` (optional)
   - Password: `password123`
   - Gender: `Male`
   - Birth Date: `1985-05-20`
   - **Profile Photo**: Upload image
   - Status: `Active`
2. Submit

**Expected Result**:
- ✅ Auto-generated employee_id (no manual input)
- ✅ Profile photo stored in `storage/app/public/employees/`
- ✅ Employee appears in list with ID visible

---

### 2b. READ
**URL**: `http://127.0.0.1:8081/employee/employees`

**Expected Result**:
- ✅ **Employee ID displayed in table**
- ✅ Full Name, Email, Gender, Entry Date visible
- ✅ Can navigate to edit/delete from this page

---

### 2c. UPDATE
**URL**: Click Edit from employee list

**Expected Result**:
- ✅ Current profile photo shows as preview
- ✅ Can upload new photo or leave blank (keeps existing)
- ✅ Updated employee_id unchanged

---

### 2d. DELETE
**URL**: Click Delete from employee list

**Expected Result**:
- ✅ Employee marked Inactive
- ✅ Removed from active list
- ✅ Employee ID no longer visible

---

## Test Scenario 3: TEACHER CRUD

### 3a. CREATE
**URL**: `http://127.0.0.1:8081/employee/teachers/create`

**Steps**:
1. Fill form:
   - NPK: `TH001`
   - First Name: `Budi`
   - Last Name: `Santoso`
   - Password: `password123`
   - Gender: `Male`
   - Birth Date: `1980-03-10`
   - **Profile Photo**: Upload image
   - Status: `Active`
2. Submit

**Expected Result**:
- ✅ Auto-generated teacher_id
- ✅ Profile photo in `storage/app/public/teachers/`
- ✅ Teacher appears in list

---

### 3b. READ
**URL**: `http://127.0.0.1:8081/employee/teachers`

**Expected Result**:
- ✅ **Teacher ID displayed in table**
- ✅ All teacher details visible with ID

---

## Test Scenario 4: OTHER MASTERS

### 4a. CLASS CRUD
**URL**: `http://127.0.0.1:8081/employee/classes/create`

**Expected Result**:
- ✅ **Class ID auto-generated** (no manual input field)
- ✅ Class ID displayed in list

### 4b. SUBJECT CRUD
**URL**: `http://127.0.0.1:8081/employee/subjects/create`

**Expected Result**:
- ✅ **Subject ID auto-generated**
- ✅ Subject ID displayed in list

### 4c. ARTICLE CRUD
**URL**: `http://127.0.0.1:8081/employee/articles/create`

**Expected Result**:
- ✅ **Article ID auto-generated**
- ✅ Article ID displayed in list

### 4d. EVENT CRUD
**URL**: `http://127.0.0.1:8081/employee/events/create`

**Expected Result**:
- ✅ **Event ID auto-generated**
- ✅ Event ID displayed in list

---

## Verification Checklist

### ✅ CREATE Operations
- [ ] Student creates with auto ID
- [ ] Employee creates with auto ID
- [ ] Teacher creates with auto ID
- [ ] Class creates with auto ID
- [ ] Subject creates with auto ID
- [ ] Article creates with auto ID
- [ ] Event creates with auto ID
- [ ] All accept profile_photo (where applicable)
- [ ] Status field present and working

### ✅ READ Operations
- [ ] Student list shows student_id
- [ ] Employee list shows employee_id
- [ ] Teacher list shows teacher_id
- [ ] Class list shows class_id
- [ ] Subject list shows subject_id
- [ ] Article list shows article_id
- [ ] Event list shows event_id
- [ ] All show correct data columns

### ✅ UPDATE Operations
- [ ] Student edit form pre-populates all data
- [ ] Student profile photo preview displays
- [ ] Can upload new profile photo
- [ ] Update saves changes correctly
- [ ] Employee/Teacher updates work same way
- [ ] Status can be changed

### ✅ DELETE Operations
- [ ] Student delete marks as Inactive
- [ ] Student removed from active list
- [ ] Employee/Teacher delete works same
- [ ] Other masters delete works
- [ ] Success message appears

### ✅ File Upload
- [ ] Profile photo accepts JPEG, PNG, JPG, GIF
- [ ] File size limit 2MB enforced
- [ ] Photos stored in correct directories
- [ ] Photos display in edit preview
- [ ] Can replace with new photo

### ✅ Menus
- [ ] All 12 menus display without errors
- [ ] Employees menu → list displays
- [ ] Teachers menu → list displays
- [ ] Students menu → list displays
- [ ] Classes menu → list displays
- [ ] Subjects menu → list displays
- [ ] Academic Years menu → list displays
- [ ] Grades menu → list displays
- [ ] Articles menu → list displays
- [ ] Tag Articles menu → list displays
- [ ] Events menu → list displays
- [ ] Tag Events menu → list displays
- [ ] Settings menu → list displays

### ✅ Error Handling
- [ ] Required field validation works
- [ ] Validation error messages display
- [ ] Duplicate record validation works (where applicable)
- [ ] Form data retained on validation error
- [ ] File type validation works (images only)
- [ ] File size validation works (max 2MB)

---

## Common Issues & Solutions

### Issue: "Route not defined" error
**Solution**: Already fixed - all routes defined in `routes/web.php`

### Issue: Profile photo not uploading
**Solution**: Check that form has `enctype="multipart/form-data"`
- ✅ All forms already have this

### Issue: Manual ID input field appears in create form
**Solution**: Already fixed - all ID fields removed from create forms
- ✅ IDs now auto-generate from database

### Issue: Duplicate Last Name field in Employee form
**Solution**: Already fixed in `resources/views/employees/create.blade.php`

### Issue: Profile photo not displaying in edit
**Solution**: Check that model has `profile_photo` attribute
- ✅ All models have this field

---

## Database Backup

Before testing, backup your database:
```bash
# SQL Server backup (recommended before major changes)
BACKUP DATABASE [SMP_IT_HANIYA] TO DISK = 'C:\backup\smp_it_haniya_backup.bak'
```

Or use file backup:
```bash
cd database
# File is at: database/SMP_IT_HANIYA.bak
```

---

## Success Criteria

✅ **All CRUD operations pass** if:
1. ✅ Can CREATE new records with auto-generated IDs
2. ✅ Can READ all records with IDs visible in lists
3. ✅ Can UPDATE records with profile photo upload
4. ✅ Can DELETE records (marked inactive)
5. ✅ No error messages appear
6. ✅ All menus work without "Route not defined" errors
7. ✅ Profile photos upload and display correctly
8. ✅ Forms validate correctly
9. ✅ Data persists after operations
10. ✅ IDs display in index/list views

---

**Test Status**: Ready for comprehensive testing ✅
**Last Updated**: November 19, 2025
