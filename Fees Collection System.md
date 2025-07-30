I want to develop an application (using laravel 11 using mysql and bootstrap 5, js) for deposition of tuition fees from students.

A Students table to store student details:
    id
    application_number
    admission_test_roll_no
    programme_name
    batch
    student_id
    section
    first_name
    last_name
    district
    address
    category (Unreserved, SC, ST, OBC)
    dob
    gender
    email
    alternate_email
    mobile
    alternate_mobile
    whatsapp
    is_pwbd
    occupation
    father_name
    mother_name
    father_occupation
    mother_occupation
    family_income
    image
    selection_type
    score_A
    score_B
    score_C
    score_D
    status
    note
    remarks
    created_by
    updated_by
    created_at
    updated_at
    deleted_at




Fees Type: 
    Library Security Deposit
    Monthly Tuition Fees
-----
fees_types [it can be Json file or a table].
    id
    title such as "Library Security Deposit", "Monthly Tuition Fees"
    slug
    description
    status
    created_at
    updated_at
    deleted_at

A table to store the Fees details:

should include the following fields:
    id
    month (dropdown with options like January, February, etc.) if applicable (its for tuition fees)
    fees_type (dropdown from fees_types)
    student_enroll_id
    application_number
    student_id
    programme_name
    batch
    first_name
    last_name
    section
    category
    gender
    email
    mobile
    whatsapp
    dob
    fee_amount
    paid_amount
    assign_date
    due_date
    pay_date
    payment_method (dropdown with options like Cash, Bank Transfer, UPI QR, Payment Gateway etc.)
    note
    remarks
    status (dropdown with options like Pending, Paid, Failed)
    created_by
    updated_by
    created_at
    updated_at
    deleted_at


    An Authentication Page where students can log in using Student Id and Date of birth, then simple dashboard to show tuition fees need to pay their fees and also show tuition fees payment history.

    Make sure use modern, maintainable, scalable with beautiful UI that supports localization and light and dark mode theme like profession level with propper error handling, validation, security aspect, use logging, flash messages, try catch, transaction etc.



