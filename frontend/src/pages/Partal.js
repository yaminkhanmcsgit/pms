import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import Loading from '../components/Loading';

function Partal() {
    const [partal, setPartal] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        document.title = 'پڑتال - Land Record System';
        fetchPartal();
    }, []);

    const fetchPartal = async () => {
        try {
            const response = await api.get('/api/partal');
            setPartal(response.data.partal.data || []);
        } catch (error) {
            console.error('Error fetching partal:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="container" dir="rtl">
            <style>
                {`
                    thead th {
                        border: 1px solid #ddd8d8 !important;
                    }
                    @media print { .no-print { display: none; } }
                `}
            </style>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <Link to="/partal/create" className="btn btn-success">
                    <i className="fa fa-plus"></i> نیا ریکارڈ شامل کریں
                </Link>
                <center><h3 className="mb-0">گوشوارہ پڑتال رپورٹ</h3></center>
            </div>

            <div className="table-responsive">
                <table className="table table-striped table-hover table-bordered text-center" style={{ width: '100%' }}>
                    <thead className="thead-dark">
                        <tr>
                            <th rowSpan="2">سیریل نمبر</th>
                            <th colSpan="6">بنیادی معلومات</th>
                            <th colSpan="2">پڑتال پیمائش موقع</th>
                            <th colSpan="2">تصدیق آخیر ملکیت وغیرہ بر موقع</th>
                            <th colSpan="2">تصدیق آخیر شجرہ نسب</th>
                            <th colSpan="2">تصدیق ملکیت و قبضہ کاشت وغیرہ</th>
                            <th rowSpan="2">تبصرہ</th>
                            <th rowSpan="2">عمل</th>
                        </tr>
                        <tr>
                            <th>ضلع نام</th>
                            <th>تحصیل نام</th>
                            <th>موضع نام</th>
                            <th>پٹواری نام</th>
                            <th>اہلکار نام</th>
                            <th>تاریخ پڑتال</th>
                            <th>تصدیق ملکیت/پیمود شدہ نمبرات خسرہ</th>
                            <th>تعداد برامدہ بدرات</th>
                            <th>تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ</th>
                            <th>تعداد برامدہ بدرات</th>
                            <th>تعداد گھری</th>
                            <th>تعداد برامدہ بدرات</th>
                            <th>مقابلہ کھتونی ہمراہ کاپی چومنڈہ</th>
                            <th>تعداد برامدہ بدرات</th>
                        </tr>
                    </thead>
                    <tbody>
                        {partal.map(record => (
                            <tr key={record.id}>
                                <td>{record.id}</td>
                                <td>{record.districtNameUrdu}</td>
                                <td>{record.tehsilNameUrdu}</td>
                                <td>{record.mozaNameUrdu}</td>
                                <td>{record.patwari_nam} {record.patwari_title || ''}</td>
                                <td>{record.ahalkar_nam} {record.ahalkar_title || ''}</td>
                                <td>{record.tareekh_partal ? new Date(record.tareekh_partal).toLocaleDateString() : ''}</td>
                                <td className="text-center">{record.tasdeeq_milkiat_pemuda_khasra || ''}</td>
                                <td className="text-center">{record.tasdeeq_milkiat_pemuda_khasra_badrat || ''}</td>
                                <td className="text-center">{record.tasdeeq_milkiat_qabza_kasht_khasra || ''}</td>
                                <td className="text-center">{record.tasdeeq_milkiat_qabza_kasht_badrat || ''}</td>
                                <td className="text-center">{record.tasdeeq_shajra_nasab_guri || ''}</td>
                                <td className="text-center">{record.tasdeeq_shajra_nasab_badrat || ''}</td>
                                <td className="text-center">{record.muqabala_khatoni_chomanda || ''}</td>
                                <td className="text-center">{record.muqabala_khatoni_chomanda_badrat || ''}</td>
                                <td>{record.tabsara || ''}</td>
                                <td>
                                    {record.operator_id == sessionStorage.getItem('operator_id') && (
                                        <Link to={`/partal/edit/${record.id}`} className="btn btn-sm btn-warning">
                                            <i className="fa fa-edit"></i> ترمیم
                                        </Link>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default Partal;