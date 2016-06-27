        FloodManager – documentation:

    Components:
        •	Entry
            o	Is an entity;
            o	Represents a flood entry. Flood entries will be used to determine flood attempts on certain user-defined events.
            o	Fields:
                	$event: The event identifier. Each event has a unique name and reffers to an action in the application (e.g. user login, user registration);
                	$source: The source identifier (e.g. the client’s IP address or hostname);
                	$created: The entry creation time;
                	$expired: The moment when the entry will expire;
        •	Manager
            o	The flood manager service. It is used to determine flood attempts. For usage, consider you have certain events in your application (e.g. user
            login, user registration) and these events are caused by sources (e.g. the user, which is determined mainly by IP address). Every time a watched
            event is caused, you should register an entry for the current source (with the addEntry method). If that event is caused by more than a defined
            number of times by a single source, then the service will consider this to be a flood attempt (the isEventAllowed method).
            o	Functions:
                	alterTime(\DateTime $time, $interval, $after = true): Adds/subtracts a time interval to/from a given DateTime object.
                	addEntry($event, $source, $window = 3600): Registers an entry for the given event and source.
                	isEventAllowed($event, $source, $threshold, $window = 3600): Determines if a source still has access to a given action.
