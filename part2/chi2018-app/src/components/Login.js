import React from 'react';
/**
 * Creates login form for users to enter their details
 * 
 * @author Alex Tuersley
 */
class Login extends React.Component {

render() {
  return (
    <div style={{marginTop:"10px"}}>
       <input
         type='text' 
         placeholder='email'
         value={this.props.email}
         onChange={this.props.handleEmail}
       />
       <input
         type='password' 
         placeholder='password'
         value={this.props.password}
         onChange={this.props.handlePassword}
       />
      <button onClick={this.props.handleLoginClick}>Log in</button>
    </div>
  );
}
}

export default Login;