Admin:

	from vanilla 
		- h::get looks for config in Child > Parent > Q - load which are found first
		- files are merged to provide default values
		- first save produces _config file

		--- what about new updates from Q OR Parent ? loaded config files control contexts / tasks shown, so new items will show in admin with new values... 
		- RESET ALL / CONTEXT / TASK ... need to find way to allow this to happen, filters and meta boxes

Frontend:

- checks for /_config - if not found, bulk, as this is required - logged in notice to visit config page to build out
- if found, loads single _config file.. and go!

Extended Contexts:

- Must be registered via code hook, as this allows to connect callback method
- Once registered, they should appear in the options page ( via an extra lookup, via exteng/get... ) ---- so, no more seperate config files???


Partials are just markup.. so might not require a callback.. ??
