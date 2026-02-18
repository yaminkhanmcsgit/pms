import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import { Bar, Doughnut } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

function Dashboard() {
    const [counts, setCounts] = useState({});
    const [roleId, setRoleId] = useState(1); // Fetch from session or API
    const [chartData, setChartData] = useState({});

    useEffect(() => {
        document.title = 'ڈیش بورڈ - Land Record System';
        // Fetch counts
        api.get('/api/dashboard-counts').then(response => setCounts(response.data));
        // Fetch chart data
        loadChartData('current_year');
    }, []);

    const loadChartData = (period) => {
        // Fetch and set chart data similar to Blade
        api.get(`/api/chart-data/grievances?period=${period}`).then(response => {
            const data = response.data;
            setChartData(prev => ({ ...prev, grievances: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'شکایات',
                    data: Object.values(data),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            } }));
        });
        // Add other charts
    };

    const cards = [
        { key: 'operators', title: 'صارفین', link: '/operators', bg: 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)', show: roleId === 1 },
        { key: 'employees', title: 'ملازمین', link: '/employees', bg: 'linear-gradient(135deg, #2196F3 0%, #1976D2 100%)', show: roleId === 1 },
        { key: 'completion_process', title: 'تکمیلی عمل', link: '/completion-process', bg: 'linear-gradient(135deg, #FF9800 0%, #F57C00 100%)', show: true },
        { key: 'partal', title: 'پڑتال', link: '/partal', bg: 'linear-gradient(135deg, #F44336 0%, #D32F2F 100%)', show: true },
        { key: 'grievances', title: 'شکایات', link: '/grievances', bg: 'linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%)', show: true },
        { key: 'settings', title: 'ترتیبات', link: '/settings', bg: 'linear-gradient(135deg, #607D8B 0%, #455A64 100%)', show: roleId === 1 },
    ];

    return (
        <div style={{ padding: '20px', direction: 'rtl' }}>
            <div style={{ display: 'flex', justifyContent: 'center', flexWrap: 'wrap' }}>
                {cards.filter(card => card.show).map(card => (
                    <div key={card.key} style={{ margin: '10px', borderRadius: '15px', boxShadow: '0 10px 30px rgba(0,0,0,0.2)', background: card.bg, color: '#fff', height: '140px', display: 'flex', flexDirection: 'column', width: '200px' }}>
                        <div style={{ padding: '15px 20px 10px', background: 'rgba(255,255,255,0.1)', textAlign: 'center' }}>
                            <h4 style={{ margin: 0, fontSize: '1.1em', fontWeight: 600 }}>{`(${counts[card.key] || 0}) ${card.title}`}</h4>
                        </div>
                        <div style={{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '10px 20px' }}>
                            <Link to={card.link} style={{ background: 'rgba(255,255,255,0.2)', border: '2px solid rgba(255,255,255,0.3)', color: '#fff', padding: '8px 16px', borderRadius: '25px', textDecoration: 'none', fontWeight: 500 }}>دیکھیں</Link>
                        </div>
                    </div>
                ))}
            </div>

            {/* Charts */}
            <div style={{ marginTop: '40px' }}>
                <div style={{ display: 'flex', flexWrap: 'wrap' }}>
                    <div style={{ flex: '1 1 50%', padding: '10px' }}>
                        <div style={{ border: '1px solid #ddd', borderRadius: '5px' }}>
                            <div style={{ padding: '15px', background: '#f5f5f5' }}>
                                <h4>شکایات</h4>
                                <select onChange={(e) => loadChartData(e.target.value)} style={{ marginBottom: '10px' }}>
                                    <option value="current_year">موجودہ سال</option>
                                    {/* Add other options */}
                                </select>
                            </div>
                            <div style={{ padding: '15px' }}>
                                {chartData.grievances && <Bar data={chartData.grievances} />}
                            </div>
                        </div>
                    </div>
                    {/* Add other charts similarly */}
                </div>
            </div>
        </div>
    );
}

export default Dashboard;
