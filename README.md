stemville
=========

This is intended to be a relatively stable repository. For small code examples/demos/tests, please use *experimental*. Avoid putting files in here that aren't used in our application.

Directory Structure
----------
Please try to keep the directory structure clean and organized to improve readability and clarity.

for **php** backend files (*for instance, all RESTful pages*) use **backend/**
for **JavaScript** (*separate, independent files*) use **js/**
for **CSS** (*stylesheets*) use **css/**
for **images** (*part of webpage or not*) use **img/**
for **partials** (*html files loaded through ajax*) use **partials/** and put an *underscore* before the file name: *_filename.html*
for **plugins** (such as highcharts) use **plugins/** to separate 3rd-party software from our own

Reminder
--------
In order to avoid broken code, make sure you always do a **git pull** before committing anything to ensure you have the latest version of the repository. *Also avoid doing a **git add --all** if you only have a couple of files to add.*