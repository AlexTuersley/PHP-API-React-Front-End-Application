import React from 'react';
import Schedule from './Schedule.js';
/**
 * Gets list of days within a schedule and passes them to Schedule Component
 * 
 * @author Alex Tuersley
 */
class Schedules extends React.Component{

  constructor(props) {
    super(props);
    this.state = {
      data:[]
    }
  }
    componentDidMount() {
        const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/schedule"
        fetch(url)
          .then( (response) => response.json() )
          .then((data) => {
          this.setState({data:data.data})
        })
          .catch ((err) => {
            console.log("something went wrong ", err)
          }
        );
    }

    render() {          
        return (
          <div>
            <h1>Schedule</h1>
            { 
              this.state.data.map( (details, i) => (<Schedule key={i} details={details} />) )
            }
          </div>
        );
      }
     
}
export default Schedules;