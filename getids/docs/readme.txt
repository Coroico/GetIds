-------------------- 
3PC: GetIds
--------------------

Version: 1.0
Date: 2011.11.27
Author: Coroico <coroico@wangba.fr>
Editor: Coroico <coroico@wangba.fr>

A general purpose snippet to get a list of resource ids for MODx 2.0.

IMPORTANT: take care of the order of arguments. To be excluded the id should be already in the list
&ids=`18, 19, -19, 20` => '18,20'          but &ids=`18, -19, 19, 20` => '18,19,20'
