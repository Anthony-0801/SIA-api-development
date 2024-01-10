###Problem 1 (Fixed by Andrei)

##Dashboard for Unpaid Function Not Working in View.html 

Misfunctioning Unpaid Function. The system successfully saves unpaid student profiles in the database. However, the dashboard appears to be inconsistent in counting and displaying unpaid status. Changing the payment status from "paid" to "unpaid" or vice versa is recorded in the database but not displayed in the dashboard.

##Explanation:

A software bug in the dashboard payment status update code may explain this discrepancy. To validate its accurate portrayal of the database payment status, the dashboard component's reasoning and operations must be assessed. Debugging and testing this system component will help discover and fix the issue, ensuring that the dashboard accurately displays student profile payment status.

##Recommendation

Review the backend code for the 'Unpaid'

###Problem 2

##The alert doesn't work when saving data

The alert functionality is not working properly while saving data. Specifically, when establishing a student profile and attempting to save it, the alert that should confirm successful saving does not appear. This prevents users from obtaining fast feedback on the success of the save operation. This operational deficiency can result in ambiguity regarding the status of the stored data.

##Explanation:

There is a malfunction in the system's ability to save student profiles in this scenario. More precisely, following the creation of a student profile and the attempt to save it, the anticipated notification indicating a successful save is not being displayed. The lack of response generates ambiguity for users, since they are unable to promptly ascertain whether the student profile has been stored successfully.

##Recommendation

The malfunctioning notification when storing student profiles can be fixed by thoroughly reviewing the code that activates the alert and ensuring its seamless interaction with the save process. Check error messages and console logs for clues to the alert's absence. Complete testing to ensure functionality and consider adding error handling to provide users with accurate feedback on failed save attempts.

###Problem 3 (Already added by Roland.)

##The print function has not been added yet. 

##Problem 4
Officer In Charge should be a drop-down of positions.

##Problem 5
Student name accepts integer values. It should accept characters only.

##Problem 6
Student Id Accepts characters. There's no validation method. It should accept Integers only.

##SUGGESTIONS

1. Convert the input fields for semester, year level, section, and selection status into dropdowns instead of text boxes. (Fixed by Andrei)
2. Modify the CSS for the update. (Fixed by Roland)
3. Replace the icon on the dashboard with something more relevant. (Fixed by Roland. Icons designed by Aljon)
4. Removing the option from the right navigation; it might be better without it. (Fixed by Roland)
5. Change Search students to "students" only.

