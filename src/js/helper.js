export const LEVEL_NOTICE = 'notice';
export const LEVEL_WARNING = 'warning';
export const LEVEL_ERROR = 'error';
export const LEVEL_CRITICAL = 'critical';

export function colorLevel(issue) {
  switch (issue.level) {
    case LEVEL_NOTICE:
      return 'blue';
    case LEVEL_WARNING:
      return 'yellow';
    case LEVEL_ERROR:
      return 'orange';
    case LEVEL_CRITICAL:
      return 'red';
  }
}

export function iconLevel(issue) {
  switch (issue.level) {
    case LEVEL_NOTICE:
      return 'info';
    case LEVEL_WARNING:
      return 'warning';
    case LEVEL_ERROR:
      return 'bug';
    case LEVEL_CRITICAL:
      return 'fire';
  }
}