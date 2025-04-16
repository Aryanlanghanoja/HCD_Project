<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        :root {
            --grey: #F1F0F6;
            --dark-grey: #8D8D8D;
            --light: #fff;
            --dark: #000;
            --green: #81D43A;
            --light-green: #E3FFCB;
            --blue: #02959F;
            --light-blue: #D0E4FF;
            --dark-blue: #0C5FCD;
            --red: #FC3B56;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--grey);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--light);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--grey);
        }

        .header h1 {
            color: var(--dark);
            font-size: 24px;
        }

        .header .search {
            position: relative;
        }

        .header .search input {
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid var(--grey);
            width: 250px;
            outline: none;
        }

        .header .search input:focus {
            border-color: var(--blue);
        }

        .faculty-table {
            width: 100%;
            border-collapse: collapse;
        }

        .faculty-table thead {
            background-color: var(--blue);
            color: var(--light);
        }

        .faculty-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }

        .faculty-table tbody tr {
            border-bottom: 1px solid var(--grey);
            transition: all 0.3s ease;
        }

        .faculty-table tbody tr:hover {
            background-color: var(--grey);
        }

        .faculty-table td {
            padding: 12px 15px;
            color: var(--dark);
        }

        .faculty-table .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }

        .faculty-table .status.active {
            background-color: var(--light-green);
            color: var(--green);
        }

        .faculty-table .status.on-leave {
            background-color: var(--light-blue);
            color: var(--dark-blue);
        }

        .faculty-table .actions {
            display: flex;
            gap: 8px;
        }

        .faculty-table .actions button {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .faculty-table .actions .view {
            background-color: var(--light-blue);
            color: var(--dark-blue);
        }

        .faculty-table .actions .edit {
            background-color: var(--light-green);
            color: var(--green);
        }

        .faculty-table .actions .delete {
            background-color: #FFE2E5;
            color: var(--red);
        }

        .faculty-table .actions button:hover {
            opacity: 0.8;
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 5px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid var(--blue);
            background-color: var(--light);
            color: var(--blue);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button.active {
            background-color: var(--blue);
            color: var(--light);
        }

        .pagination button:hover:not(.active) {
            background-color: var(--grey);
        }

        @media screen and (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header .search input {
                width: 100%;
            }

            .faculty-table thead {
                display: none;
            }

            .faculty-table, .faculty-table tbody, .faculty-table tr, .faculty-table td {
                display: block;
                width: 100%;
            }

            .faculty-table tr {
                margin-bottom: 15px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                border-radius: 5px;
                overflow: hidden;
            }

            .faculty-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid var(--grey);
            }

            .faculty-table td:before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: 600;
                text-align: left;
            }

            .faculty-table td:last-child {
                border-bottom: 0;
            }

            .faculty-table .actions {
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Faculty List</h1>
            <div class="search">
                <input type="text" placeholder="Search faculty...">
            </div>
        </div>

        <table class="faculty-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td data-label="ID">F001</td>
                    <td data-label="Name">Dr. John Smith</td>
                    <td data-label="Department">Computer Science</td>
                    <td data-label="Position">Professor</td>
                    <td data-label="Contact">john.smith@university.edu</td>
                    <td data-label="Status"><span class="status active">Active</span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <button class="view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button>
                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-label="ID">F002</td>
                    <td data-label="Name">Dr. Sarah Johnson</td>
                    <td data-label="Department">Mathematics</td>
                    <td data-label="Position">Associate Professor</td>
                    <td data-label="Contact">sarah.johnson@university.edu</td>
                    <td data-label="Status"><span class="status active">Active</span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <button class="view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button>
                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-label="ID">F003</td>
                    <td data-label="Name">Prof. Michael Chen</td>
                    <td data-label="Department">Physics</td>
                    <td data-label="Position">Assistant Professor</td>
                    <td data-label="Contact">michael.chen@university.edu</td>
                    <td data-label="Status"><span class="status on-leave">On Leave</span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <button class="view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button>
                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-label="ID">F004</td>
                    <td data-label="Name">Dr. Emily Rodriguez</td>
                    <td data-label="Department">Biology</td>
                    <td data-label="Position">Professor</td>
                    <td data-label="Contact">emily.rodriguez@university.edu</td>
                    <td data-label="Status"><span class="status active">Active</span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <button class="view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button>
                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-label="ID">F005</td>
                    <td data-label="Name">Dr. Robert Davis</td>
                    <td data-label="Department">Chemistry</td>
                    <td data-label="Position">Associate Professor</td>
                    <td data-label="Contact">robert.davis@university.edu</td>
                    <td data-label="Status"><span class="status active">Active</span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <button class="view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button>
                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-label="ID">F006</td>
                    <td data-label="Name">Prof. Linda Kim</td>
                    <td data-label="Department">Psychology</td>
                    <td data-label="Position">Professor</td>
                    <td data-label="Contact">linda.kim@university.edu</td>
                    <td data-label="Status"><span class="status on-leave">On Leave</span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <button class="view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button>
                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <button>Prev</button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>Next</button>
        </div>
    </div>
</body>
</html>