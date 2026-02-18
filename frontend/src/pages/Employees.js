import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import Loading from '../components/Loading';

function Employees() {
    const [employees, setEmployees] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        document.title = 'ملازمین - Land Record System';
        fetchEmployees();
    }, []);

    const fetchEmployees = async () => {
        try {
            const response = await api.get('/api/employees');
            setEmployees(response.data.employees || []);
        } catch (error) {
            console.error('Error fetching employees:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="container" dir="rtl">
            <div className="d-flex justify-content-between align-items-center mb-3">
                <Link to="/employees/create" className="btn btn-success">
                    <i className="fa fa-plus"></i> ملازم شامل کریں
                </Link>
            </div>

            <center><legend><h3>ملازمین کی فہرست</h3></legend></center>

            <div className="table-responsive">
                <table className="table table-striped table-hover">
                    <thead className="thead-dark text-center">
                        <tr>
                            <th>نمبر شمار</th>
                            <th>نام</th>
                            <th>والد کا نام</th>
                            <th>نام ضلع</th>
                            <th>نام تحصیل</th>
                            <th>نام موضع</th>
                            <th>فون</th>
                            <th>شناختی کارڈ</th>
                            <th>تعلیم</th>
                            <th>اہلکار کی قسم</th>
                            <th>تاریخ شمولیت</th>
                            <th className="no-print">عمل</th>
                        </tr>
                    </thead>
                    <tbody className="text-center">
                        {employees.map(emp => (
                            <tr key={emp.id}>
                                <td>{emp.id}</td>
                                <td>{emp.nam}</td>
                                <td>{emp.walid_ka_nam}</td>
                                <td>{emp.district_name}</td>
                                <td>{emp.tehsil_name}</td>
                                <td>{emp.moza_name}</td>
                                <td>{emp.phone}</td>
                                <td>{emp.cnic}</td>
                                <td>{emp.darja_taleem}</td>
                                <td>{emp.employee_type_title}</td>
                                <td>{emp.tareekh_shamil ? new Date(emp.tareekh_shamil).toLocaleDateString() : ''}</td>
                                <td>
                                    <Link to={`/employees/edit/${emp.id}`} className="btn btn-sm btn-warning">
                                        <i className="fa fa-edit"></i> ترمیم
                                    </Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default Employees;