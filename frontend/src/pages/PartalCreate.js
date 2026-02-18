import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api';
import Loading from '../components/Loading';

function PartalCreate() {
    const [formData, setFormData] = useState({
        zila_id: '',
        tehsil_id: '',
        moza_id: '',
        patwari_nam: '',
        ahalkar_nam: '',
        tareekh_partal: '',
        tasdeeq_milkiat_pemuda_khasra: '',
        tasdeeq_milkiat_pemuda_khasra_badrat: '',
        tasdeeq_milkiat_qabza_kasht_khasra: '',
        tasdeeq_milkiat_qabza_kasht_badrat: '',
        tasdeeq_shajra_nasab_guri: '',
        tasdeeq_shajra_nasab_badrat: '',
        muqabala_khatoni_chomanda: '',
        muqabala_khatoni_chomanda_badrat: '',
        tabsara: ''
    });
    const [districts, setDistricts] = useState([]);
    const [tehsils, setTehsils] = useState([]);
    const [mozas, setMozas] = useState([]);
    const [employees, setEmployees] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const navigate = useNavigate();

    useEffect(() => {
        document.title = 'نیا پڑتال ریکارڈ شامل کریں - Land Record System';
        loadInitialData();
    }, []);

    const loadInitialData = async () => {
        try {
            const [districtsRes, employeesRes] = await Promise.all([
                api.get('/api/districts'),
                api.get('/api/employees')
            ]);
            setDistricts(districtsRes.data);
            setEmployees(employeesRes.data);
        } catch (error) {
            console.error('Error loading initial data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleDistrictChange = async (districtId) => {
        setFormData(prev => ({ ...prev, zila_id: districtId, tehsil_id: '', moza_id: '' }));
        if (districtId) {
            try {
                const response = await api.get(`/api/tehsils?district_id=${districtId}`);
                setTehsils(response.data);
                setMozas([]);
            } catch (error) {
                console.error('Error loading tehsils:', error);
            }
        } else {
            setTehsils([]);
            setMozas([]);
        }
    };

    const handleTehsilChange = async (tehsilId) => {
        setFormData(prev => ({ ...prev, tehsil_id: tehsilId, moza_id: '' }));
        if (tehsilId) {
            try {
                const response = await api.get(`/api/mozas?tehsil_id=${tehsilId}`);
                setMozas(response.data);
            } catch (error) {
                console.error('Error loading mozas:', error);
            }
        } else {
            setMozas([]);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            await api.post('/api/partal', formData);
            navigate('/partal');
        } catch (error) {
            console.error('Error creating partal record:', error);
            alert('Error creating record');
        } finally {
            setSubmitting(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="container" dir="rtl">
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="panel-title">نیا پڑتال ریکارڈ شامل کریں</h3>
                </div>
                <div className="panel-body">
                    <form onSubmit={handleSubmit} className="form-modern">
                        <div className="row">
                            <div className="form-group col-md-4 col-xs-12">
                                <label>نام ضلع</label>
                                <select
                                    name="zila_id"
                                    value={formData.zila_id}
                                    onChange={(e) => handleDistrictChange(e.target.value)}
                                    className="form-control urdu-input"
                                    required
                                >
                                    <option value="">منتخب کریں</option>
                                    {districts.map(district => (
                                        <option key={district.zila_id} value={district.zila_id}>
                                            {district.zilaNameUrdu}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>نام تحصیل</label>
                                <select
                                    name="tehsil_id"
                                    value={formData.tehsil_id}
                                    onChange={(e) => handleTehsilChange(e.target.value)}
                                    className="form-control urdu-input"
                                    required
                                >
                                    <option value="">منتخب کریں</option>
                                    {tehsils.map(tehsil => (
                                        <option key={tehsil.tehsil_id} value={tehsil.tehsil_id}>
                                            {tehsil.tehsilNameUrdu}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>نام موضع</label>
                                <select
                                    name="moza_id"
                                    value={formData.moza_id}
                                    onChange={handleChange}
                                    className="form-control urdu-input"
                                    required
                                >
                                    <option value="">منتخب کریں</option>
                                    {mozas.map(moza => (
                                        <option key={moza.moza_id} value={moza.moza_id}>
                                            {moza.mozaNameUrdu}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div className="row">
                            <div className="form-group col-md-4 col-xs-12">
                                <label>نام پٹواری</label>
                                <select
                                    name="patwari_nam"
                                    value={formData.patwari_nam}
                                    onChange={handleChange}
                                    className="form-control urdu-input"
                                    required
                                >
                                    <option value="">منتخب کریں</option>
                                    {employees.map(emp => (
                                        <option key={emp.id} value={emp.nam}>
                                            {emp.nam}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>نام اہلکار</label>
                                <select
                                    name="ahalkar_nam"
                                    value={formData.ahalkar_nam}
                                    onChange={handleChange}
                                    className="form-control urdu-input"
                                    required
                                >
                                    <option value="">منتخب کریں</option>
                                    {employees.map(emp => (
                                        <option key={emp.id} value={emp.nam}>
                                            {emp.nam}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تاریخ پڑتال</label>
                                <input
                                    type="date"
                                    name="tareekh_partal"
                                    value={formData.tareekh_partal}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                        </div>
                        <div className="row">
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تصدیق ملکیت پیمودہ/خسراہ</label>
                                <input
                                    type="number"
                                    name="tasdeeq_milkiat_pemuda_khasra"
                                    value={formData.tasdeeq_milkiat_pemuda_khasra}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تصدیق ملکیت پیمودہ/بدرات</label>
                                <input
                                    type="number"
                                    name="tasdeeq_milkiat_pemuda_khasra_badrat"
                                    value={formData.tasdeeq_milkiat_pemuda_khasra_badrat}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تصدیق ملکیت قبضہ/کاشت/خسراہ</label>
                                <input
                                    type="number"
                                    name="tasdeeq_milkiat_qabza_kasht_khasra"
                                    value={formData.tasdeeq_milkiat_qabza_kasht_khasra}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                        </div>
                        <div className="row">
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تصدیق ملکیت قبضہ/کاشت/بدرات</label>
                                <input
                                    type="number"
                                    name="tasdeeq_milkiat_qabza_kasht_badrat"
                                    value={formData.tasdeeq_milkiat_qabza_kasht_badrat}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تصدیق شجرہ نسب گوری</label>
                                <input
                                    type="number"
                                    name="tasdeeq_shajra_nasab_guri"
                                    value={formData.tasdeeq_shajra_nasab_guri}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تصدیق شجرہ نسب بدرات</label>
                                <input
                                    type="number"
                                    name="tasdeeq_shajra_nasab_badrat"
                                    value={formData.tasdeeq_shajra_nasab_badrat}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                        </div>
                        <div className="row">
                            <div className="form-group col-md-4 col-xs-12">
                                <label>مقابلہ کھاتونی چومندہ</label>
                                <input
                                    type="number"
                                    name="muqabala_khatoni_chomanda"
                                    value={formData.muqabala_khatoni_chomanda}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>مقابلہ کھاتونی چومندہ بدرات</label>
                                <input
                                    type="number"
                                    name="muqabala_khatoni_chomanda_badrat"
                                    value={formData.muqabala_khatoni_chomanda_badrat}
                                    onChange={handleChange}
                                    className="form-control"
                                />
                            </div>
                            <div className="form-group col-md-4 col-xs-12">
                                <label>تبصرہ</label>
                                <input
                                    type="text"
                                    name="tabsara"
                                    value={formData.tabsara}
                                    onChange={handleChange}
                                    className="form-control urdu-input"
                                />
                            </div>
                        </div>
                        <button type="submit" className="btn btn-success" disabled={submitting}>
                            <i className="fa fa-save"></i> {submitting ? 'محفوظ ہو رہا ہے...' : 'محفوظ کریں'}
                        </button>
                        <button type="button" className="btn btn-secondary" onClick={() => navigate('/partal')} style={{ marginLeft: '10px' }}>
                            واپس
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default PartalCreate;