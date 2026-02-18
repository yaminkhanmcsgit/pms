import React from 'react';
import { Routes, Route } from 'react-router-dom';
import Login from './components/Login';
import Layout from './components/Layout';
import Dashboard from './components/Dashboard';
import Grievances from './pages/Grievances';
import Partal from './pages/Partal';
import PartalCreate from './pages/PartalCreate';
import Employees from './pages/Employees';

function App() {
    return (
        <Routes>
            <Route path="/" element={<Login />} />
            <Route path="/dashboard" element={<Layout><Dashboard /></Layout>} />
            <Route path="/grievances" element={<Layout><Grievances /></Layout>} />
            <Route path="/partal" element={<Layout><Partal /></Layout>} />
            <Route path="/partal/create" element={<Layout><PartalCreate /></Layout>} />
            <Route path="/employees" element={<Layout><Employees /></Layout>} />
            {/* Add more routes as needed */}
        </Routes>
    );
}

export default App;
