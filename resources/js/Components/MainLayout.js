import React from 'react';
import List from './Elements/List';
function MainLayout() {
    return (
        <div className="container mt-5">
            <div className="row justify-content-center">
                <div className="col-md-8">
                    <div className="card text-center">
                        <div className="card-header"><h2>React Component in Laravel</h2></div>

                        <div className="card-body">I'm tiny React component in Laravel app!</div>
                        <div className="card-body">I'm Andrey Kayerov!</div>
                        <List/>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default MainLayout;

