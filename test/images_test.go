package codereview

import (
	"testing"
	"net/url"
	"fmt"
)

func TestImages_post(t *testing.T) {
	c := NewClient()
	registerUser(c)
	res := c.post("images", url.Values{"link": {"https://avatars1.githubusercontent.com/u/15997353?v=3&s=200"},
		"imageId":{"abcdef"}, "description": {"It's my avatar"}});
	fmt.Println(res)
}

func TestImages_get(t *testing.T) {

}
