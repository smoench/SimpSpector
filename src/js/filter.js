import React from "react";
import {render} from "react-dom";
import FilteredIssueList from "./components/FilteredIssueList";

const rootEl = document.getElementById('filter');
const id = rootEl.getAttribute('data-commit-id');

render(
  <FilteredIssueList id={id}/>,
  rootEl
);