import React, { useEffect, useState } from "react";
import axios from "axios";

function Customers() {
    const [customers, setCustomers] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchCustomers();
    }, []);

    const fetchCustomers = async () => {
        const response = await axios.get("/api/customers");
        setCustomers(response.data.data);
        setLoading(false);
    };

    const deleteCustomer = async (id) => {
        await axios.delete(`/api/customers/${id}`);
        fetchCustomers();
    };

    return (
        <div>
            <h2>Customers List</h2>
            {loading ? (
                <p>Loading...</p>
            ) : (
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>Email</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {customers.map((customer) => (
                            <tr key={customer.id}>
                                <td>{customer.id}</td>
                                <td>{customer.name}</td>
                                <td>{customer.email}</td>
                                <td>
                                    <button onClick={() => deleteCustomer(customer.id)}>Delete</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
        </div>
    );
}

export default Customers;
