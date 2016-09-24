import React from "react";
import Issue from "./Issue";
import {iconLevel, colorLevel} from '../helper';

export default class IssueGroup extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      open: false
    }
  }

  toggle() {
    this.setState({
      open: !this.state.open
    });
  }

  render() {
    return (
      <div>
        <div className="title" onClick={() => this.toggle()}>
          {this.renderBar()}
          <i className="dropdown icon"/>
          <i className="file icon"/>
          {this.props.file}
        </div>
        <div className={`content ${this.state.open ? 'active' : ''}`}>
          {this.state.open ? this.props.issues.map(issue => {
            let key = issue.file + issue.line + issue.level + issue.gadget + issue.title;
            return <Issue key={key} issue={issue}/>
          }) : null }
        </div>
      </div>
    );
  }

  renderBar() {
    var levels = new Map();
    levels.set('notice', 0);
    levels.set('warning', 0);
    levels.set('error', 0);
    levels.set('critical', 0);

    for (let issue of this.props.issues) {
      levels.set(issue.level, levels.get(issue.level)+1);
    }

    return (
      <div style={{float: "right"}}>
        {[...levels.entries()].map(([level, count]) => {
          if (count == 0) {
            return;
          }

          return (
            <span className="ui" key={level} title={level}>
              <i className={`${colorLevel(level)} ${iconLevel(level)} icon`}/>
              {count}
            </span>
          );
        })}
      </div>
    );
  }
}
