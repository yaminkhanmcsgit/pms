import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import Loading from '../components/Loading';

function Grievances() {
    const [grievances, setGrievances] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        document.title = 'شکایات - Land Record System';
        fetchGrievances();
    }, []);

    const fetchGrievances = async () => {
        try {
            const response = await api.get('/api/grievances');
            setGrievances(response.data.grievances.data || []);
        } catch (error) {
            console.error('Error fetching grievances:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="container">
            <div className="mb-3">
                <Link to="/grievances/create" className="btn btn-success pull-right">
                    <i className="fa fa-plus"></i> Add New Grievance
                </Link>
            </div>

            <center><legend><h3>Grievances List</h3></legend></center>

            <div className="table-responsive">
                <table className="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant Name</th>
                            <th>Father Name</th>
                            <th>CNIC</th>
                            <th>District</th>
                            <th>Tehsil</th>
                            <th>Mouza</th>
                            <th>Grievance Type</th>
                            <th>Status</th>
                            <th>Application Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {grievances.map(grievance => (
                            <tr key={grievance.id}>
                                <td>{grievance.id}</td>
                                <td>{grievance.applicant_name}</td>
                                <td>{grievance.father_name}</td>
                                <td>{grievance.cnic}</td>
                                <td>{grievance.district_name}</td>
                                <td>{grievance.tehsil_name}</td>
                                <td>{grievance.moza_name}</td>
                                <td>{grievance.grievance_type_name}</td>
                                <td><span className={`label label-${grievance.status_color}`}>{grievance.status_name}</span></td>
                                <td>{grievance.application_date}</td>
                                <td>
                                    <div className="dropdown">
                                        <button className="btn btn-sm btn-default dropdown-toggle actions-dropdown-btn" type="button" data-toggle="dropdown">
                                            <i className="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul className="dropdown-menu actions-dropdown-menu">
                                            <li><a href="#" onClick={() => viewGrievance(grievance.id)}><i className="fa fa-eye"></i> View</a></li>
                                            <li><a href="#" onClick={() => editGrievance(grievance.id)}><i className="fa fa-edit"></i> Edit</a></li>
                                            <li><a href="#" onClick={() => deleteGrievance(grievance.id)}><i className="fa fa-trash"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );

    function viewGrievance(id) {
        // Implement view modal
        console.log('View grievance', id);
    }

    function editGrievance(id) {
        // Navigate to edit
        console.log('Edit grievance', id);
    }

    function deleteGrievance(id) {
        // Implement delete
        console.log('Delete grievance', id);
    }
}

export default Grievances;