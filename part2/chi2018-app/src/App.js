import React from 'react';
import { BrowserRouter as Router, Switch, Route, NavLink } from "react-router-dom";
import Home from './components/Home';
import Schedules from './components/Schedules';

import './App.css';

function App() {
  return (
    <Router>
      <div className="App">
        <nav>
          <ul>
            <li>
              <NavLink activeClassName="selected" exact to="/">Home</NavLink>
            </li>
            <li>
              <NavLink activeClassName="selected" to="/schedules">Schedule</NavLink>
            </li>
            <li>
              <NavLink activeClassName="selected" to="/admin">Admin</NavLink>
            </li>
          </ul>
        </nav>
        <Switch>
          <Route path="/schedules">
            <Schedules />
          </Route>
          <Route path="/admin">
            Admin
          </Route>
          <Route path="/">
            <Home/>
          </Route>
          <Route path="*">
            404 Not Found
          </Route>
        </Switch>
      </div>
   </Router>
  );
}

export default App;
