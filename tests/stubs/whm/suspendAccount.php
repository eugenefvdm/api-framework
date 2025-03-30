<?php

#--------------------------------------------------------------------------------------
# Instructions:
#--------------------------------------------------------------------------------------
# <theme-name> is the theme assigned to cPanel account.
# 1) cd /usr/local/cpanel/base/frontend/<theme-name>
# 2) mkdir api_examples
# 3) cd api_examples
# 4) create a file Email_suspend_login.live.pl and put this code into that file.
# 5) In your browser login to a cPanel account.
# 6) Manually change the url from: .../frontend/<theme-name>/
#    to .../frontend/<theme-name>/api_examples/Email_suspend_login.live.pl
#--------------------------------------------------------------------------------------

use strict;

use Cpanel::LiveAPI ();

my $cpanel = Cpanel::LiveAPI->new(); # Connect to cPanel - only do this once.

# Print the header
print "Content-type: text/plain\r\n\r\n";

# Call the API
my $response = $cpanel->uapi(
    q/Email/,
    q/suspend_login/,
    {
        'email' => 'user@example.com',
    }
);

# Handle the response
if ($response->{cpanelresult}{result}{status}) {
    my $data = $response->{cpanelresult}{result}{data};
    # Do something with the $data
    # So you can see the data shape we print it here.
    print to_json($data);
}
else {
    # Report errors:
    print to_json($response->{cpanelresult}{result}{errors});
}

# Disconnect from cPanel - only do this once.
$cpanel->end();

#--------------------------------------------------------------------------------------
# Helper function to convert a perl object to html printable json
#--------------------------------------------------------------------------------------
sub to_json {
    require JSON;
    my $str = JSON->new->pretty->encode($_[0]);
    return $str;
}
