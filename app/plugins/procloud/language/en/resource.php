<?php
/*
 * DEVPROM (http://www.devprom.net)
 * resource.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <marketing@devprom.ru>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 $plugin_text_array = array 
 (
 	'Текущая итерация' => '',
 	1 => 'project management tool projects teams outsourcing community news tasks vacancies services projects development traceability methodology',
 	2 => 'release message what\'s new blog articles success stories read more scope iteration request defect user story comment subscribe',
 	3 => 'Development teams, professional developers',
 	4 => 'team device description enterprise interests participants current projects completed projects metrics indicators services velocity effectiveness outsourcing development freelance search for a team and job',
 	5 => 'Public projects',
 	6 => 'project users recommendation public favorite recommended interesting tags cloud description user story add blog development plan release roadmap source code vacancies ask question participants downloads',
 	7 => 'Community professionals catalog, developers and IT specialists',
 	8 => 'participant member skills active community developer',
 	9 => '',
 	21 => 'Active projects, participants visited their projects last month',
 	22 => 'Active projects, participants visited their projects last year',
 	23 => 'Projects which have issues and tasks',
 	24 => 'Users logged into their projects last month',
 	25 => 'Users logged into their projects last year',
 	89 => 'member developer quality manager programmer analyst professional skills knowledge tools team effectiveness teams invite works participates took advises comments .net c# mysql msmql database microsoft java j2ee',
 	90 => 'Opened tasks list to share for outsourcing',
 	91 => 'user story project respond task solution help required outsourcing freelance free-lance',
 	92 => 'Project management advises exchange between community members and projects participants',
 	93 => 'advise exchange experience new useful helpful project management task completion planning artifacts documentation requirements testing management',
 	94 => 'Opened projects vacancies',
 	95 => 'vacancy project opened employment description requirements responses get done asap IT professional requires make decision interesting role programmer tester quality engineer designer analyst developer',
 	96 => 'Services catalog for your project team from the community members',
 	97 => 'service services category development testing training teaching project management administration offer useful cost programming database',
 	98 => 'Features tour',
 	99 => 'devprom features tool deployment application software development companies product management development testing requirements analysis traceability project metrics reporting enterprise user feedback user story monitoring implementation collaboration plan backlog iteration release functionality rating',
 	100 => 'Manage your teams, create new teams and invite community members. Best team is able to get best project.',
 	101 => 'Publish your projects, demonstrate what are you doing to users. Collect feedback, enhancements and issues from community members.',
 	102 => 'Help project members to implement their tasks. It is the way you can demonstrate your experience and value. Publish issues your project have no time to implement. There are community members want to help you.',
 	103 => 'Share your experience and best practices using advises. This is the thing is required to all community members to successfully complete their projects and don\'t repeat your mistakes.',
 	104 => 'Publish your projects vacancies, there are community members which want to help you and participate in your projects.',
 	105 => 'Publish your services which can help community to complete their projects, place it in the market and support it.',
 	106 => 'If you have your web site already you can integrate <a href="http://www.devprom.net/co/feedback/">feedback form</a> in it, then users can post enhancements or report bugs using the form.',
 	108 => 'Hello, %1!'.chr(10).chr(10).'You have registered in the system <a href="%5">DEVPROM</a>. To authorize please use your login and password:'.Chr(10).Chr(10).'Login: %2'.Chr(10).'Password: %3'.Chr(10).Chr(10).'To fully use the system please activate your account using the following link: %4',
 	109 => 'Please activate your account in your <a href="/co/profile.php">profile</a>',

 	119 => 'Hello %4!'.chr(10).chr(10).'User %1 has sent you the message "%2":'.chr(10).chr(10).'%3',
 	120 => 'Hello!'.chr(10).chr(10).'There is a new comment on message "%1":'.chr(10).chr(10).'%2',

 	123 => 'User %1 has sent new message "%2" to the team "%4":'.chr(10).chr(10).'%3',

 	169 => 'Describe your skills and experience',
 	170 => 'Enter your professional skills',
 	171 => 'Issue implementation suggestion has been added',
 	172 => 'New advise is added',
 	173 => 'To accept an advise follow the link',
 	174 => 'You have new message',
 	175 => 'Service request',
 	176 => 'Reply on vacancy has been added',
 	177 => 'Account modification',
 	178 => 'Message subject',
 	179 => 'Message text',
 	180 => 'It is unable to send the message',
 	181 => 'The message has been sent successfully',
 	182 => 'Your implementation suggestion was added already',
 	183 => 'Is it unable to add your implementation suggestion',
 	184 => 'Issue implementation suggestion has been added successfully', 

 	241 => 'Such a project doesn\'t exist',
 	242 => 'You can\'t add issues into the project, because the project is private',
 	243 => 'Users advises by theme "%1"',
 	244 => 'Tags should be separated by comma. Using the tags you can classify your project, shortly describe your product. Tags are used to build projects catalogue.',
 	245 => 'You can also choose from existing tags',

 	303 => 'Report more advises, your rating depends on advises usefulness',
 	304 => 'Provide qualitative services to participants of projects',
 	305 => 'Provide your help to projects, report new issues or vote for the issues, vote for valuable project',
 	306 => 'Participate in teams, organize new effective teams',
 	307 => 'Participate in projects, share your projects state with users',
 	308 => 'Effectively use time when resolve tasks in your projects',
 	309 => 'Velocity of issues implementation describes your efficiency',
 	310 => 'More you resolve tasks in your projects, more valuable you stands before customers and investors',
	311 => 'Fill you profile as much as possible, describe your skills and interests',
	312 => 'Higher rating of team members, higher rating of the whole team',
	313 => 'Average team velocity is based on team velocity in team\'s projects, more velocity more effective your team',
	314 => 'More effective your team use time during projects more valuable it is for a customer',
	315 => 'Less bugs are in your projects more qualitative your team',
 	316 => 'Ratings',
 	317 => 'Rating participant user team project vote rate customer efficiency velocity selection choose more better',
 	318 => 'Ratings are used to estimate level of users, teams and projects. The system collects lot of indexes to provide you objective understanding if project is valuable, to gather an effective team or separate developers to solve your tasks. Here are the actions should be done by users to have higher rate of their profile, teams and projects.',
 	319 => 'Public as much information as possible on the project\'s wiki',
 	320 => 'Use the system to resolve issues and bugs in your project',
 	321 => 'Post topics in the project\'s blog, share project state and release notes with users',
 	322 => 'Public project artifacts, let users to download intermediate builds or completed releases of your products',
 	323 => 'Share tasks of your projects with other users, just outsource it',
 	324 => 'Plan iterations and tasks in the system, this makes the development process to be transparent for users',
 	325 => 'When users are interested in the project they are voting or even adding the project to favorites to be closer to the development process',
 	326 => 'What is DEVPROM? Mission and goals, our development team.',
 	327 => 'what is devprom goals mission tasks authors development team ALM teams requirements management test management',
	328 => 'Licensing and pricing. Tech support. DEVPROM online. DEVPROM local installation. Support plans.',
 	329 => 'licensing pricing price DEVPROM online team edition local server own plans license free',

 	941 => 'Questions and issues',
 	1000 => 'Demo project is prepearing now',
 	1001 => 'Demo project template is required',
 	1002 => 'Unable to create demo project'
);

?>