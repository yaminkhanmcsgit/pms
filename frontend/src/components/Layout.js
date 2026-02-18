import React, { useState, useEffect } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import api from '../api';

const styles = `
body {
    background: #f0f2f5;
    font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', serif;
    padding-bottom: 60px;
}

.form-group {
    float: right;
}
.panel-heading {
    text-align: center;
}
.panel{max-width: 1200px;margin: 0 auto;}

/* Urdu input fields */
.urdu-input, .urdu-input * {
    font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', serif !important;
    direction: rtl !important;
    unicode-bidi: embed !important;
}

.urdu-input,
.urdu-input *,
.dropdown-menu,
td,
th,
select,
option {
    line-height: 2.2em !important;
    vertical-align: middle !important;
}
td {
    white-space: nowrap !important;
}
select,
option {
    font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', serif !important;
    direction: rtl;
    text-align: right;
    font-size: 0.7rem !important;       /* Smaller font */
    line-height: 1.6em !important;      /* Adequate height */
    padding-top: 2px !important;
    padding-bottom: 2px !important;
    height: auto !important;
    vertical-align: middle;
}
ul.notika-menu-wrap li a {
    padding: 15px 13px !important;
}

/* Force right-to-left for Notika menu */
.rtl-menu {
    direction: rtl !important;
    display: flex !important;
    justify-content: flex-end !important;
}

.rtl-menu li {
    float: right !important;
    margin-left: 10px; /* space between items */
}

.rtl-menu li a {
    text-align: right !important;
    color: white !important;
    white-space: nowrap !important;
}

.rtl-menu li a i {
    color: white !important;
}

/* Active tab styling */
.rtl-menu li.active a {
    font-weight: bold;
    background: none !important;
    padding: 15px 10px !important;
}

/* Dropdown submenu styling */
.desktop-dropdown-menu .dropdown-menu li {
    display: block !important;
    width: 100%;
}

.desktop-dropdown-menu .dropdown-menu li a {
    color: #333 !important;
}

.desktop-dropdown-menu .dropdown-menu li a i {
    color: #333 !important;
}

.footer-copyright-area {
    position: fixed;
    bottom: 0;
    width: 100%;
    z-index: 1000;
}

@media (max-width: 700px) {
    .dashboard-panels {
        flex-direction: column !important;
    }
    .dashboard-panels > div {
        width: 100% !important;
        margin-bottom: 10px !important;
    }
}

.mean-container .mean-bar::after {
    content: "";
}

@media print {
    a, button , .dashboard-panels .panel { display: none !important; }
    body, html { width: 100%; margin: 0; padding: 0; }
    .container { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
}
.container{width:100% !important;}

.ajax-spinner {
    position: relative;
}
.ajax-spinner .double-bounce1, .ajax-spinner .double-bounce2 {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: #337ab7;
    opacity: 0.6;
    position: absolute;
    top: 0;
    left: 0;
    animation: sk-bounce 2.0s infinite ease-in-out;
}
.ajax-spinner .double-bounce2 {
    animation-delay: -1.0s;
}
@keyframes sk-bounce {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}
`;

function Layout({ children }) {
    const [logo, setLogo] = useState('/notika/img/logo/logo.png');
    const [footerText, setFooterText] = useState('Revenue & Estate Department Khyber Pakhtunkhwa');
    const [notifications, setNotifications] = useState({ count: 0, list: [] });
    const [user, setUser] = useState({ name: '', roleId: 1 });
    const navigate = useNavigate();

    useEffect(() => {
        // Fetch settings
        api.get('/api/settings').then(response => {
            if (response.data) {
                if (response.data.logo_path) {
                    const fullLogoUrl = api.defaults.baseURL + '/assets/logo/' + response.data.logo_path;
                    setLogo(fullLogoUrl);
                    // Set favicon
                    const favicon = document.querySelector('link[rel="icon"]') || document.querySelector('link[rel="shortcut icon"]');
                    if (favicon) {
                        favicon.href = fullLogoUrl;
                        console.log('Setting favicon to:', fullLogoUrl);
                    }
                }
                if (response.data.footer_text) setFooterText(response.data.footer_text);
            }
        });

        // Fetch user info from API
        api.get('/api/user').then(response => {
            const userData = response.data;
            setUser({ name: userData.name, roleId: userData.role_id });
        }).catch(error => {
            console.error('Error fetching user data:', error);
            // Fallback to default if API fails
            setUser({ name: 'User', roleId: 1 });
        });

        // Fetch notifications
        fetchNotifications();
        const interval = setInterval(fetchNotifications, 30000);

        // Initialize UrduWriter
        const initUrduWriter = () => {
            if (window.UrduWriter) window.UrduWriter.enable('.urdu-input');
            document.querySelectorAll('.urdu-input').forEach(el => {
                el.setAttribute('lang', 'ur');
                el.style.fontFamily = "'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', serif";
                el.style.direction = 'rtl';
            });
        };
        if (!window.UrduWriter) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/gh/urduwriter/urduwriter@master/urduwriter.min.js';
            script.onload = initUrduWriter;
            document.head.appendChild(script);
        } else {
            initUrduWriter();
        }

        return () => clearInterval(interval);
    }, []);

    const fetchNotifications = () => {
        api.get('/api/notifications').then(response => {
            const data = response.data;
            const total = data.recent_partal + data.recent_completion_process + data.recent_grievances + data.pending_grievances;
            setNotifications({ count: total, list: data });
        });
    };

    const handleLogout = () => {
        api.post('/api/logout').then(() => navigate('/'));
    };

    const menuItems = [
        { to: '/dashboard', icon: 'notika-house', label: 'ڈیش بورڈ' },
        ...(user.roleId === 1 ? [{ to: '/operators', icon: 'notika-support', label: 'صارفین' }] : []),
        ...(user.roleId === 1 ? [{ to: '/employees', icon: 'notika-social', label: 'ملازمین' }] : []),
        { to: '/completion-process', icon: 'notika-edit', label: 'تکمیلی کام' },
        { to: '/partal', icon: 'notika-form', label: 'پڑتال' },
        { to: '/grievances', icon: 'notika-flag', label: 'شکایات' },
        { to: '/reports', icon: 'notika-bar-chart', label: 'رپورٹس' },
        ...(user.roleId === 1 ? [{ to: '/contactus', icon: 'notika-mail', label: 'رابطہ' }] : []),
        ...(user.roleId === 1 ? [{ to: '/news', icon: 'notika-newspaper', label: 'خبریں' }] : []),
        { to: '/settings', icon: 'fa fa-gear', label: 'ترتیبات' },
    ];

    return (
        <div>
            <style>{styles}</style>
            {/* Header */}
            <div className="header-menu-area" style={{ backgroundColor: '#5cb85c' }}>
                <div className="container-fluid">
                    <div className="row align-items-center">
                        <div className="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <div className="logo-area" style={{ padding: 0 }}>
                                <NavLink to="/dashboard">
                                    <img src={logo} alt="Logo" style={{ maxHeight: '60px' }} />
                                </NavLink>
                            </div>
                        </div>
                        <div className="col-lg-7 col-md-7 hidden-xs hidden-sm">
                            <ul className="nav nav-tabs notika-menu-wrap menu-it-icon-pro rtl-menu justify-content-center">
                                <li>
                                    <NavLink to="/dashboard" className={({ isActive }) => isActive ? 'active' : ''}>
                                        <i className="notika-icon notika-house"></i> ڈیش بورڈ
                                    </NavLink>
                                </li>
                                {user.roleId === 1 && (
                                    <>
                                        <li>
                                            <NavLink to="/operators" className={({ isActive }) => isActive ? 'active' : ''}>
                                                <i className="notika-icon notika-support"></i> صارفین
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/employees" className={({ isActive }) => isActive ? 'active' : ''}>
                                                <i className="notika-icon notika-social"></i> ملازمین
                                            </NavLink>
                                        </li>
                                    </>
                                )}
                                <li>
                                    <NavLink to="/completion-process" className={({ isActive }) => isActive ? 'active' : ''}>
                                        <i className="notika-icon notika-edit"></i> تکمیلی کام
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink to="/partal" className={({ isActive }) => isActive ? 'active' : ''}>
                                        <i className="notika-icon notika-form"></i> پڑتال
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink to="/grievances" className={({ isActive }) => isActive ? 'active' : ''}>
                                        <i className="notika-icon notika-flag"></i> شکایات
                                    </NavLink>
                                </li>
                                <li className="dropdown desktop-dropdown-menu">
                                    <a href="#" className="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i className="notika-icon notika-more"></i> دیگر <span className="caret"></span>
                                    </a>
                                    <ul className="dropdown-menu">
                                        <li>
                                            <NavLink to="/reports" className={({ isActive }) => isActive ? 'active' : ''}>
                                                <i className="notika-icon notika-bar-chart"></i> رپورٹس
                                            </NavLink>
                                        </li>
                                        {user.roleId === 1 && (
                                            <>
                                                <li>
                                                    <NavLink to="/contactus" className={({ isActive }) => isActive ? 'active' : ''}>
                                                        <i className="notika-icon notika-mail"></i> رابطہ
                                                    </NavLink>
                                                </li>
                                                <li>
                                                    <NavLink to="/news" className={({ isActive }) => isActive ? 'active' : ''}>
                                                        <i className="notika-icon notika-form"></i> خبریں
                                                    </NavLink>
                                                </li>
                                            </>
                                        )}
                                        <li>
                                            <NavLink to="/settings" className={({ isActive }) => isActive ? 'active' : ''}>
                                                <i className="fa fa-gear"></i> ترتیبات
                                            </NavLink>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div className="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                            <div className="header-top-menu">
                                <ul className="nav navbar-nav notika-top-nav justify-content-end justify-content-center justify-content-lg-end">
                                    <li className="nav-item nc-al">
                                        <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" className="nav-link dropdown-toggle">
                                            <span><i className="notika-icon notika-alarm"></i></span>
                                            <div className="spinner4 spinner-4" style={{ top: '-2px', left: '32px' }}></div>
                                            <div className="ntd-ctn" style={{ top: '3px', left: '39px' }}>
                                                <span>{notifications.count}</span>
                                            </div>
                                        </a>
                                        <div role="menu" className="dropdown-menu message-dd notification-dd animated zoomIn">
                                            <div className="hd-mg-tt">
                                                <h2>اطلاعات</h2>
                                            </div>
                                            <div className="hd-message-info" id="notification-list">
                                                {/* Render notifications */}
                                                {notifications.list.recent_partal > 0 && (
                                                    <a href="/partal">
                                                        <div className="hd-message-sn">
                                                            <div className="hd-message-img">
                                                                <i className="notika-icon notika-form" style={{ fontSize: '24px', color: '#337ab7' }}></i>
                                                            </div>
                                                            <div className="hd-mg-ctn">
                                                                <h3>نئے پڑتال اندراجات</h3>
                                                                <p>آخری 7 دنوں میں {notifications.list.recent_partal} نئے اندراجات</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                )}
                                                {notifications.list.recent_completion_process > 0 && (
                                                    <a href="/completion-process">
                                                        <div className="hd-message-sn">
                                                            <div className="hd-message-img">
                                                                <i className="notika-icon notika-edit" style={{ fontSize: '24px', color: '#5cb85c' }}></i>
                                                            </div>
                                                            <div className="hd-mg-ctn">
                                                                <h3>نئے تکمیلی کام اندراجات</h3>
                                                                <p>آخری 7 دنوں میں {notifications.list.recent_completion_process} نئے اندراجات</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                )}
                                                {notifications.list.recent_grievances > 0 && (
                                                    <a href="/grievances">
                                                        <div className="hd-message-sn">
                                                            <div className="hd-message-img">
                                                                <i className="notika-icon notika-flag" style={{ fontSize: '24px', color: '#f0ad4e' }}></i>
                                                            </div>
                                                            <div className="hd-mg-ctn">
                                                                <h3>نئے شکایات اندراجات</h3>
                                                                <p>آخری 7 دنوں میں {notifications.list.recent_grievances} نئی شکایات</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                )}
                                                {notifications.list.pending_grievances > 0 && (
                                                    <a href="/grievances">
                                                        <div className="hd-message-sn">
                                                            <div className="hd-message-img">
                                                                <i className="notika-icon notika-alert" style={{ fontSize: '24px', color: '#d9534f' }}></i>
                                                            </div>
                                                            <div className="hd-mg-ctn">
                                                                <h3>زیر التواء شکایات</h3>
                                                                <p>{notifications.list.pending_grievances} شکایات زیر التواء ہیں</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                )}
                                                {notifications.count === 0 && (
                                                    <div className="text-center" style={{ padding: '20px' }}>کوئی نئی اطلاع نہیں</div>
                                                )}
                                            </div>
                                            <div className="hd-mg-va">
                                                <a href="/dashboard">تمام دیکھیں</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li className="nav-item dropdown">
                                        <a href="#" id="profileDropdown" data-toggle="dropdown" role="button" aria-expanded="false" className="nav-link dropdown-toggle">
                                            <span><i className="fa fa-user-circle"></i></span>
                                            <span style={{ marginRight: '8px' }}>{user.name}</span>
                                        </a>
                                        <div role="menu" className="dropdown-menu dropdown-menu-right animated fadeIn" style={{ minWidth: '180px', padding: '15px' }}>
                                            <button className="btn btn-info btn-block" style={{ marginBottom: '10px' }} onClick={() => window.$('#changePassModal').modal('show')}>پاسورڈ تبدیل کریں</button>
                                            <button className="btn btn-danger btn-block" onClick={handleLogout}>لاگ آوٹ</button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Mobile Menu */}
            <div className="mobile-menu-area hidden-md hidden-lg">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div className="mobile-menu">
                                <nav id="dropdown">
                                    <ul className="mobile-menu-nav">
                                        <li>
                                            <NavLink to="/dashboard">
                                                <i className="notika-icon notika-house"></i> ڈیش بورڈ
                                            </NavLink>
                                        </li>
                                        {user.roleId === 1 && (
                                            <>
                                                <li>
                                                    <NavLink to="/operators">
                                                        <i className="notika-icon notika-support"></i> صارفین
                                                    </NavLink>
                                                </li>
                                                <li>
                                                    <NavLink to="/employees">
                                                        <i className="notika-icon notika-social"></i> ملازمین
                                                    </NavLink>
                                                </li>
                                            </>
                                        )}
                                        <li>
                                            <NavLink to="/completion-process">
                                                <i className="notika-icon notika-edit"></i> تکمیلی کام
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/partal">
                                                <i className="notika-icon notika-form"></i> پڑتال
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/grievances">
                                                <i className="notika-icon notika-flag"></i> شکایات
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/reports">
                                                <i className="notika-icon notika-bar-chart"></i> رپورٹس
                                            </NavLink>
                                        </li>
                                        {user.roleId === 1 && (
                                            <>
                                                <li>
                                                    <NavLink to="/contactus">
                                                        <i className="notika-icon notika-mail"></i> رابطہ
                                                    </NavLink>
                                                </li>
                                                <li>
                                                    <NavLink to="/news">
                                                        <i className="notika-icon notika-newspaper"></i> خبریں
                                                    </NavLink>
                                                </li>
                                            </>
                                        )}
                                        <li>
                                            <NavLink to="/settings">
                                                <i className="fa fa-gear"></i> ترتیبات
                                            </NavLink>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Content */}
            <div className="content" style={{ padding: '20px' }}>
                <br /><br />
                {children}
            </div>

            {/* Footer */}
            <div className="footer-copyright-area" dir="ltr" style={{ background: '#d4edda', position: 'fixed', bottom: 0, width: '100%', zIndex: 1000 }}>
                <div className="container">
                    <div className="row">
                        <div className="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div className="footer-copy-right">
                                <p style={{ color: '#333' }}>
                                    © {new Date().getFullYear()}
                                    {footerText}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Global AJAX Loading Overlay */}
            <div id="globalAjaxLoader" style={{ display: 'none', position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', background: 'rgba(0,0,0,0.5)', zIndex: 9999, textAlign: 'center' }}>
                <div style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%, -50%)', background: 'white', padding: '20px', borderRadius: '5px', boxShadow: '0 2px 10px rgba(0,0,0,0.3)' }}>
                    <div className="ajax-spinner" style={{ width: '40px', height: '40px', margin: '0 auto' }}>
                        <div className="double-bounce1"></div>
                        <div className="double-bounce2"></div>
                    </div>
                    <div style={{ marginTop: '15px', fontSize: '16px', color: '#333', fontFamily: "'Noto Nastaliq Urdu', serif" }}>
                        براہ کرم انتظار کریں...
                    </div>
                </div>
            </div>

            {/* Change Password Modal */}
            <div className="modal fade" id="changePassModal" tabIndex="-1" role="dialog" aria-labelledby="changePassModalLabel">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="row1">
                            <form method="POST" action="/change-password">
                                <div className="modal-header" style={{ background: '#2c3e50', color: '#fff' }}>
                                    <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 className="modal-title" id="changePassModalLabel">پاسورڈ تبدیل کریں</h4>
                                </div>
                                <div className="modal-body">
                                    <div id="changePassMsg"></div>
                                    <div className="form-group col-md-12" style={{ marginBottom: '20px' }}>
                                        <label htmlFor="old_password">پرانا پاسورڈ</label><br />
                                        <input type="password" name="old_password" id="old_password" className="form-control" required />
                                    </div>
                                    <div className="form-group col-md-12" style={{ marginBottom: '20px' }}>
                                        <label htmlFor="new_password">نیا پاسورڈ</label><br />
                                        <input type="password" name="new_password" id="new_password" className="form-control" required />
                                    </div>
                                    <div className="form-group col-md-12" style={{ marginBottom: '0' }}>
                                        <label htmlFor="confirm_password">تصدیق پاسورڈ</label><br />
                                        <input type="password" name="confirm_password" id="confirm_password" className="form-control" required />
                                    </div>
                                </div>
                                <div className="modal-footer" style={{ textAlign: 'right' }}>
                                    <div className="col-md-12">
                                        <button type="button" className="btn btn-default" data-dismiss="modal">بند کریں</button>
                                        <button type="submit" className="btn btn-primary">محفوظ کریں</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Layout;
