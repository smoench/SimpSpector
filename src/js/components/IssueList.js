import React from "react";
import IssueGroup from "./IssueGroup";

export default class IssueList extends React.Component {
  render() {
    let groupedIssues = new Map();

    for (let issue of this.props.issues) {
      if (!groupedIssues.has(issue.file)) {
        groupedIssues.set(issue.file, []);
      }

      groupedIssues.get(issue.file).push(issue);
    }

    return (
      <div className="ui styled accordion">
        {[...groupedIssues.entries()].map(([file, issues]) => {
          return (
            <IssueGroup key={file} file={file} issues={issues}/>
          );
        })}
      </div>
    );
  }
}
