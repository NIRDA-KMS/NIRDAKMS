class ScheduleEvent {
    constructor() {
      this.eventForm = document.getElementById('eventForm');
      this.cancelBtn = document.getElementById('cancelBtn');
      this.init();
    }
  
    init() {
      this.loadAttendees();
      this.setupEventListeners();
      this.handleResponsive();
    }
  
    loadAttendees() {
      // Simulating AJAX call to fetch users
      console.log('Loading attendees...');
      
      // In a real implementation, this would be an actual fetch call:
      /*
      fetch('/api/users')
        .then(response => response.json())
        .then(data => this.populateAttendees(data.users))
        .catch(error => console.error('Error loading attendees:', error));
      */
      
      // Mock data for demonstration
      const mockUsers = [
        { id: 1, name: "John Doe" },
        { id: 2, name: "Jane Smith" },
        { id: 3, name: "Robert Johnson" },
        { id: 4, name: "Emily Davis" },
        { id: 5, name: "Michael Wilson" }
      ];
      
      this.populateAttendees(mockUsers);
    }
  
    populateAttendees(users) {
      const select = document.getElementById('eventAttendees');
      select.innerHTML = ''; // Clear existing options
      
      users.forEach(user => {
        const option = document.createElement('option');
        option.value = user.id;
        option.textContent = user.name;
        select.appendChild(option);
      });
    }
  
    setupEventListeners() {
      this.eventForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (this.validateForm()) {
          this.submitForm();
        }
      });
  
      this.cancelBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
          window.location.href = '/collaboration'; // Redirect to collaboration dashboard
        }
      });
  
      window.addEventListener('resize', () => {
        this.handleResponsive();
      });
    }
  
    validateForm() {
      const title = document.getElementById('eventTitle').value.trim();
      const start = document.getElementById('startDateTime').value;
      const end = document.getElementById('endDateTime').value;
      
      if (!title) {
        alert('Please enter an event title');
        return false;
      }
      
      if (!start || !end) {
        alert('Please select both start and end times');
        return false;
      }
      
      if (new Date(start) >= new Date(end)) {
        alert('End time must be after start time');
        return false;
      }
      
      return true;
    }
  
    submitForm() {
      const formData = new FormData(this.eventForm);
      const eventData = Object.fromEntries(formData);
      
      // Get selected attendees
      eventData.attendees = Array.from(document.getElementById('eventAttendees').selectedOptions)
        .map(option => option.value);
      
      console.log('Submitting event:', eventData);
      
      // In a real implementation, this would be an actual fetch call:
      /*
      fetch('/api/events', {
        method: 'POST',
        body: JSON.stringify(eventData),
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        }
        throw new Error('Network response was not ok');
      })
      .then(data => {
        this.showSuccessMessage();
      })
      .catch(error => {
        this.showErrorMessage(error);
      });
      */
      
      // For demonstration, we'll just show a success message
      this.showSuccessMessage();
    }
  
    showSuccessMessage() {
      alert('Event created successfully!');
      this.eventForm.reset();
    }
  
    showErrorMessage(error) {
      console.error('Error:', error);
      alert('There was an error creating the event. Please try again.');
    }
  
    handleResponsive() {
      const formRow = document.querySelector('.form-row');
      if (!formRow) return;
      
      if (window.innerWidth < 768) {
        formRow.style.flexDirection = 'column';
      } else {
        formRow.style.flexDirection = 'row';
      }
    }
  }
  
  // Initialize when DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    new ScheduleEvent();
  });