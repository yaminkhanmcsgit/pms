import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api';

function Login() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [logo, setLogo] = useState(api.defaults.baseURL + '/notika/img/logo/logo.png');
    const [backgroundImage, setBackgroundImage] = useState(`url(${api.defaults.baseURL}/notika/img/logo/logo.png)`);
    const [footerText, setFooterText] = useState('Revenue & Estate Department Khyber Pakhtunkhwa');
    const [forgotEmail, setForgotEmail] = useState('');
    const [forgotMessage, setForgotMessage] = useState('');
    const [loginLoading, setLoginLoading] = useState(false);
    const [forgotLoading, setForgotLoading] = useState(false);
    const navigate = useNavigate();

    useEffect(() => {
        api.get('/api/settings').then(response => {
            if (response.data) {
                if (response.data.logo_path) {
                    const fullLogoUrl = api.defaults.baseURL + '/assets/logo/' + response.data.logo_path;
                    setLogo(fullLogoUrl);
                    setBackgroundImage(`url(${fullLogoUrl})`);
                    // Set favicon
                    const favicon = document.querySelector('link[rel="icon"]') || document.querySelector('link[rel="shortcut icon"]');
                    if (favicon) {
                        favicon.href = fullLogoUrl;
                        console.log('Setting favicon to:', fullLogoUrl);
                    }
                }
                if (response.data.footer_text) {
                    setFooterText(response.data.footer_text);
                }
            }
        }).catch(() => {});
    }, []);

    const handleLogin = async (e) => {
        e.preventDefault();
        setLoginLoading(true);
        try {
            const response = await api.post('/api/login', { username, password });
            if (response.data.success) {
                localStorage.setItem('api_token', response.data.token);
                navigate('/dashboard');
            } else {
                setError('غلط صارف نام یا پاسورڈ');
            }
        } catch (err) {
            setError('لاگ ان ناکام');
        } finally {
            setLoginLoading(false);
        }
    };

    const handleForgotPassword = async (e) => {
        e.preventDefault();
        setForgotLoading(true);
        setForgotMessage('');
        try {
            const response = await api.post('/api/forgot-password', { email: forgotEmail });
            if (response.data.success) {
                setForgotMessage('پاسورڈ ری سیٹ کا لنک آپ کے ای میل پر بھیج دیا گیا ہے');
                setTimeout(() => window.$('#forgotPasswordModal').modal('hide'), 2000);
            } else {
                setForgotMessage('ای میل بھیجنے میں خرابی ہوئی ہے');
            }
        } catch (err) {
            setForgotMessage('ای میل بھیجنے میں خرابی ہوئی ہے');
        } finally {
            setForgotLoading(false);
        }
    };

    const styles = {
        body: {
            background: 'linear-gradient(135deg, #e8f5e8 0%, #2d7d2d 100%)',
            fontFamily: "'Noto Sans Arabic', sans-serif",
            minHeight: '100vh',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            margin: 0,
            padding: '20px',
            position: 'relative',
        },
        background: {
            content: '""',
            position: 'absolute',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundImage: backgroundImage,
            backgroundSize: '50px 50px',
            opacity: 0.09,
            pointerEvents: 'none',
        },
        container: {
            maxWidth: '400px',
            margin: '0 auto',
            background: '#fff',
            borderRadius: '8px',
            boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1)',
            padding: '40px',
            zIndex: 999999999999999,
            position: 'relative',
        },
        logoContainer: {
            textAlign: 'center',
            marginBottom: '20px',
        },
        logo: {
            maxWidth: '100px',
            height: 'auto',
        },
        title: {
            textAlign: 'center',
            fontSize: '24px',
            fontWeight: 600,
            color: '#333',
            marginBottom: '10px',
        },
        subtitle: {
            textAlign: 'center',
            color: '#666',
            marginBottom: '30px',
        },
        formGroup: {
            marginBottom: '20px',
        },
        formControl: {
            width: '100%',
            height: '50px',
            borderRadius: '6px',
            border: '1px solid #ddd',
            padding: '0 15px',
            fontSize: '16px',
            boxSizing: 'border-box',
        },
        btnLogin: {
            width: '100%',
            height: '50px',
            background: '#1877f2',
            border: 'none',
            borderRadius: '6px',
            color: '#fff',
            fontSize: '18px',
            fontWeight: 600,
            cursor: 'pointer',
            transition: 'background-color 0.2s',
        },
        alert: {
            borderRadius: '6px',
            marginBottom: '20px',
            padding: '10px',
            backgroundColor: '#f2dede',
            color: '#a94442',
            border: '1px solid #ebccd1',
        },
        footer: {
            textAlign: 'center',
            marginTop: '30px',
            paddingTop: '20px',
            borderTop: '1px solid #ddd',
            color: '#666',
            fontSize: '14px',
        },
    };

    return (
        <div style={styles.body}>
            <div style={styles.background}></div>
            <div style={styles.container}>
                <div style={styles.logoContainer}>
                    <img src={logo} alt="Logo" style={styles.logo} />
                </div>
                <div style={styles.title}>لاگ ان</div>
                <div style={styles.subtitle}>{footerText}</div>
                <form onSubmit={handleLogin}>
                    <div style={styles.formGroup}>
                        <input
                            type="text"
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                            style={styles.formControl}
                            placeholder="صارف نام"
                            required
                            dir="ltr"
                        />
                    </div>
                    <div style={styles.formGroup}>
                        <input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            style={styles.formControl}
                            placeholder="پاسورڈ"
                            required
                            dir="ltr"
                        />
                    </div>
                    {error && <div style={styles.alert}>{error}</div>}
                    <button type="submit" style={styles.btnLogin} disabled={loginLoading}>
                        {loginLoading ? (
                            <>
                                <span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {' '}لاگ ان ہو رہا ہے...
                            </>
                        ) : (
                            'لاگ ان کریں'
                        )}
                    </button>
                </form>
                <div style={{ textAlign: 'center', marginTop: '20px' }}>
                    <button
                        type="button"
                        className="btn btn-link"
                        data-toggle="modal"
                        data-target="#forgotPasswordModal"
                        style={{ color: '#1877f2', textDecoration: 'none' }}
                    >
                        پاسورڈ بھول گئے؟
                    </button>
                </div>
                <div style={styles.footer}>
                    <p dir="ltr">
                        © {new Date().getFullYear()} {footerText}
                    </p>
                </div>
            </div>

            {/* Forgot Password Modal */}
            <div className="modal fade" id="forgotPasswordModal" tabIndex="-1" role="dialog" style={{ zIndex: 999999999999999 }} aria-labelledby="forgotPasswordModalLabel">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 className="modal-title">پاسورڈ بھول گئے</h4>
                        </div>
                        <form onSubmit={handleForgotPassword}>
                            <div className="modal-body">
                                <p>اپنا ای میل درج کریں تاکہ پاسورڈ ری سیٹ کا لنک حاصل کریں</p>
                                {forgotMessage && <div className="alert alert-danger">{forgotMessage}</div>}
                                <div className="form-group">
                                    <input
                                        type="email"
                                        value={forgotEmail}
                                        onChange={(e) => setForgotEmail(e.target.value)}
                                        className="form-control"
                                        placeholder="ای میل"
                                        required
                                        dir="ltr"
                                    />
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-default" data-dismiss="modal">منسوخ کریں</button>
                                <button type="submit" className="btn btn-primary" disabled={forgotLoading}>
                                    {forgotLoading ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {' '}براہ کرم انتظار کریں...
                                        </>
                                    ) : (
                                        'لنک بھیجیں'
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Login;
